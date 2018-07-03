<template>
    <div class="box box-primary">

        <!-- box-header -->
        <div class="box-header with-border">
            <h3 class="box-title">{{trans('origanization.page_title')}}</h3>
        </div>

        <!-- /.box-header -->

        <!-- box-body -->
        <div class="box-body">
            <div>
                <menu id="nestable-menu">
                    <button type="button" id="to-expand-all" data-action="expand-all"  class="btn btn-info">{{ trans('origanization.label.label_expand') }}</button>
                    <button type="button" id="to-collapse-all"data-action="collapse-all"  class="btn btn-info">{{ trans('origanization.label.label_Collapse') }}</button>
                </menu>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <nesteable-tree :initdata ="listData" ></nesteable-tree>
                </div>
                <div class="col-lg-6">
                    <add-new-division @newdata="addDivision" :initdata = "initdata"></add-new-division>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</template>
<script>
    export default{
        props: ['initdata'],
        data () {
            return {
                listData: '',
                addNewData: ''
            };
        },
        created: function() {
            this.listData = this.initdata;
        },
        methods: {
            addDivision : function (value) {
                this.listData = value;
            }
        }
    }
</script>