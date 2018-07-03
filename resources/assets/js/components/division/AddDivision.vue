<template>
    <div>
        <div>
            <p id="title-frm">{{ trans('origanization.label.label_create') }}</p>
            <form class="form-horizontal">
                <div class="box-body">
                    <div :class="{'form-group': true, 'has-error': errors.has('origanization_name')}">
                        <label for="division-name" class="col-sm-4 control-label">{{ trans('origanization.label.label_name') }}</label>
                        <div class="col-sm-8">
                            <input type="text" name="origanization_name"  v-model="name" class="form-control" id="division-name">
                            <span v-show="errors.has('origanization_name')" class=" help-block is-danger">
                                {{ errors.all().indexOf('The origanization_name field is required.') > -1 ? trans('origanization.message.required') : trans('origanization.message.existed_name') }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{ trans('origanization.label.label_Origanization') }}</label>

                        <div class="col-sm-8">
                            <select class="form-control" name="parent" v-model="parent" id="division-parent">
                                <option value="">{{ trans('origanization.label.label_select') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="button" id="btn-add-new" @click="addNewDivision" class="btn pull-right btn-info">{{ trans('origanization.label.label_save') }}</button>
                </div>
                <!-- /.box-footer -->
            </form>
        </div>

        <div id="edit-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ trans('origanization.label.label_edit') }}</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="box-body">
                                <input type="hidden" id="item-id" v-model="id_edit">
                                <div :class="{'form-group': true, 'has-error': errors.has('origanization_name_edit')}">
                                    <label for="division-name-edit" class="col-sm-4 control-label">{{ trans('origanization.label.label_name') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="origanization_name_edit"  v-model="name_edit" class="form-control" id="division-name-edit">
                                        <span v-show="errors.has('origanization_name_edit')" class=" help-block is-danger">
                                            {{ errors.all().indexOf('The origanization_name_edit field is required.') > -1 ? trans('origanization.message.required') : trans('origanization.message.existed_name') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ trans('origanization.label.label_Origanization') }}</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" v-model="parent_edit" id="division-parent-edit">
                                            <option value="">{{ trans('origanization.label.label_select') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('origanization.label.label_close') }}</button>
                        <button type="button" @click="editDivision" class="btn  pull-right btn-info">{{ trans('origanization.label.label_save') }}</button>
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
                name: '', //origanization name
                parent: '', //choose origanization parent
                name_edit: '', //origanization name
                parent_edit: '', //choose origanization parent,
                id_edit: '', //choose origanization parent,
                currentItem: null
            };
        },
        mounted() {
            var self = this;
            $('#division-parent').append(this.convertDataToOption(this.initdata,'-').replace('undefined',''));
            $('#division-parent-edit').append(this.convertDataToOption(this.initdata,'-').replace('undefined',''));

            $(document).on('click','.edit-item',function () {
                self.$validator.clean();
                $('#edit-modal').modal('show');
                self.currentItem = $(this).closest('.dd-item');
                self.name_edit = $(this).closest('.dd-handle').text();
                self.parent_edit = self.currentItem.parent().closest('.dd-item').attr('data-id') ? self.currentItem.parent().closest('.dd-item').attr('data-id') :'';
                self.id_edit = self.currentItem.attr('data-id');
            });

            //custom ruler
            this.$validator.extend('unique',{
                getMessage: (field) => 'origanization.message.existed_name',
                validate:(value)=> {
                    return this.$http.get('/division/checkUnique?name=' + value +'&id=' +this.id_edit ).then((response) => {
                        return {
                            valid: response.body.code,
                        };
                    }, (response) => {
                            return false;
                        });
                    }
            });

            this.$validator.extend('unique_create',{
                    getMessage: (field) => 'origanization.message.required',
                    validate:(value)=> {
                        return this.$http.get('/division/checkUnique?name=' + value).then((response) => {
                            return {
                                valid: response.body.code,
                            };
                        }, (response) => {
                                return false;
                            });
                        }
            });

            this.$validator.attach('origanization_name_edit', 'required|unique');
            this.$validator.attach('origanization_name', 'required|unique_create');
        },

        methods: {
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
            addNewDivision : function () {
                var _self = this;
                var data = {name: this.name,parent_id: this.parent, _token: this.token};

                this.$validator.validate('origanization_name', this.name).then(result => {
                    if(result){
                        this.$http.post('/division/create', data).then((response)=>{
                            console.log(response.body);
                            if (response.body.code) {
                                //emit data to parent component
                                this.$emit('newdata',response.body.data);

                                //call gendering select origanization again
                                _self.initParentSelect(response.body.data);

                                this.name = '';
                                this.$validator.clean();
                                this.showMessage(this.trans('origanization.message.title_name'), response.body.message, 'success');
                            } else {
                                this.showMessage(this.trans('origanization.message.title_name'), response.body.message, 'danger');
                            }
                        }, (response)=>{
                            this.showMessage(this.trans('origanization.message.title_name'), response.status + ' ' +response.statusText, 'danger');
                        });
                    }
                });

            },
            editDivision : function () {
                var id = $('#item-id').val(),
                    _self = this;
                this.$validator.validate('origanization_name_edit', this.name_edit).then(result => {
                    if(result){
                        var data = {name: this.name_edit,parent_id: this.parent_edit, _token: this.token};
                        this.$http.post('/division/edit/' +id, data).then((response)=>{
                            if (response.body.code) {
                                //emit data to parent component
                                _self.$emit('newdata', response.body.data);

                                //call gendering select origanization again
                                this.initParentSelect(response.body.data);

                                this.name_edit = '';
                                this.$validator.clean();
                                this.showMessage(this.trans('origanization.message.title_name'), response.body.message, 'success');
                            }else {
                                this.showMessage(this.trans('origanization.message.title_name'), response.body.message, 'danger');
                            }
                        }, (response)=>{
                            this.showMessage(this.trans('origanization.message.title_name'), response.status + ' ' +response.statusText, 'danger');
                        });
                        $('#edit-modal').modal('toggle');
                    }
                });
            },

            initParentSelect: function (data) {
                $('#division-parent').html('');
                $('#division-parent-edit').html('');
                $('#division-parent').append('<option value="">'+ this.trans('origanization.label.label_select') +'</option>');
                $('#division-parent').append(this.convertDataToOption(data,'-'));
                $('#division-parent-edit').append('<option value="">'+ this.trans('origanization.label.label_select') +'</option>');
                $('#division-parent-edit').append(this.convertDataToOption(data,'-'));
            }
        }
    }
</script>