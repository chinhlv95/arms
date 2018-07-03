<template>
    <div class="cf nestable-lists" id="root-division" >
        <div class="dd" id="nestable">
        </div>

        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ trans('origanization.label.label_question') }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('origanization.label.label_close') }}</button>
                        <button type="button" class="btn  btn-info " @click="removeItem" data-dismiss="modal">{{ trans('origanization.label.label_confirm') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    export default{
        props: ['initdata'],
        data () { // define varibel
            return {
                token : $('meta[name=csrf-token]').attr('content'),
                currentItem : null
            };
        },
        mounted() {
            var self = this;
            this.renderNestable(this.initdata);
            $(document).on('click','.remove-item',function () {
                self.currentItem = $(this).closest('.dd-item');
            });

            $(document).on('click','#to-expand-all',function () {
                $('#nestable').nestable('expandAll');
            });
            $(document).on('click','#to-collapse-all',function () {
                $('#nestable').nestable('collapseAll');
            });
        },
        watch: {
            initdata: function(newValue,oldValue){
                this.renderNestable(newValue);
            }
        },
        methods: { // define function
            listify: function (strarr) {
                var self = this;
                var l = $("<ol>").addClass("dd-list");
                $.each(strarr, function(i, v) {
                    var c = $("<li>").addClass("dd-item").attr('data-id',v.id).attr('data-name',v.name),
                            h = $("<div>").addClass("dd-handle").text(v.name)
                                    .append('<div style="float: right;"><a class="dd-nodrag remove-item" data-toggle="modal" data-target="#myModal" href="#"><span class="glyphicon glyphicon-remove"></span></a></div>')
                                    .append('<div style="float: right;margin-right: 10px"><a  class="dd-nodrag edit-item" href="javascript:void(0)"><span class="glyphicon glyphicon-pencil"></span></a></div>');
                    l.append(c.append(h));
                    if (!!v["children"])
                        c.append(self.listify(v["children"]));
                });
                return l;
            },
            convertDataToOption: function (strarr,parentName) {
                var self = this;
                var l = "";
                $.each(strarr, function(i, v) {
                    var nameItem = parentName+v.name;
                    var c = '<option value="'+v.id+'">'+nameItem+'</option>';

                    l = l + c;
                    if (!!v.children)
                        var tmp =  parentName+ parentName;
                    l = l + self.convertDataToOption(v.children,tmp);
                });
                return l;
            },
            renderNestable: function(initdata){
                var self = this;
                $('#nestable').remove();
                var able = $('<div>').addClass('dd').attr('id', 'nestable');
                $('#root-division').append(able);
                var list = this.listify(initdata);
                $("#nestable").html(list);
                // activate Nestable for list 1
                $('#nestable').nestable({
//                    group: 1,
                    maxDepth:100,
                    callback : function(l,e){
                        // l is the main container
                        // e is the element that was moved
                        var itemId = e.attr('data-id');
                        var parentId = e.parent().closest('li').attr('data-id');
                        self.$http.post('/division/editParent/' +itemId, {_token: self.token, parent_id:parentId}).then((response)=>{
                            if (response.body.code) {
                                self.addSuccessMessage(response.body.message,response.body.data);
                            }else {

                            }
                        },(response)=>{
                                console.log(response);
                        });
                    }

                });
                $('#nestable').nestable('collapseAll');
            },
            removeItem: function () {
                //TODO call ajax remove item
                var id = this.currentItem.attr('data-id');
                var data = {id: id, _token: this.token};
                this.$http.post('/division/delete/'+id  ,data).then((response)=>{
                    if (response.body.code) {
                        this.addSuccessMessage(response.body.message,response.body.data);
                        var chid = this.currentItem.find('.dd-item');
                        var itemIds = [this.currentItem.attr('data-id')];
                        $.each(chid,function () {
                            itemIds.push($(this).attr('data-id'));
                        });
                        var options = $('#division-parent').find('option');
                        $.each(options,function () {
                            if($.inArray ($(this).attr('value'),itemIds ) !== -1 ) $(this).remove();
                        });
                        this.currentItem.remove();

                        var options2 = $('#division-parent-edit').find('option');
                        $.each(options2,function () {
                            if($.inArray ($(this).attr('value'),itemIds ) !== -1 ) $(this).remove();
                        });

                        this.resetFrm();
                    }else {
                        var alertElement = '<div class="row alert-message"><div class="col-md-12">' +
                                '<div class="box-body">' +
                                '<div class="alert alert-danger alert-dismissible">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                                '<h4 id="title-message">'+ this.trans('origanization.message.title_name') +'</h4>' +
                                '<p id="content-message">'+response.body.message+'</p>' +
                                '</div>' +
                                '</div>' +
                                '</div></div>';
                        $('#display-message').html(alertElement);
                        $(".alert-message").delay(3000).slideUp(300);
                    }
                }, (response)=>{
                    console.log(response);
                });
            },
            resetFrm: function () {
                $('#title-frm').text( this.trans('origanization.label.label_create'));
                $('#division-parent').val("");
                $('#division-name') .val("");
                $('#item-id').val("");
                $("#btn-add-new").show();
                $("#btn-edit").hide();
                $('#division-name').focus();
            },

            validateDivision: function(name) {
                if (!!name) {
                    return true;
                }else {
                    this.addErrorMessage($('#division-name'), this.trans('origanization.message.required'));
                    return false;
                }
            },
            addErrorMessage: function(element, errorMessage) {
                element.closest('.form-group').addClass('has-error');
                element.parent().find('.help-block').remove();
                element.parent().append('<span class="help-block" style="">'+errorMessage+'</span>');
                element.focus();
            },
            addSuccessMessage: function (message,data) {
                var alertElement = '<div class="row alert-message"><div class="col-md-12">' +
                        '<div class="box-body">' +
                        '<div class="alert alert-success alert-dismissible">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                        '<h4 id="title-message">'+ this.trans('origanization.message.title_name') +'</h4>' +
                        '<p id="content-message">'+message+'</p>' +
                        '</div>' +
                        '</div>' +
                        '</div></div>';
                $('#display-message').html(alertElement);
                $(".alert-message").delay(3000).slideUp(300);
                $('.form-group').removeClass('has-error');
                $('.form-group').find('.help-block').remove();
                $('#division-parent').html('');
                $('#division-parent').append('<option value="">'+ this.trans('origanization.label.label_select') +'</option>');
                $('#division-parent').append(this.convertDataToOption(data, '-'));
            }
        }
    }
</script>