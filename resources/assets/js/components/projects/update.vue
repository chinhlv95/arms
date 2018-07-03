<template>
    <div class="box box-primary">
        <!-- box-header -->
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('project.edit_page_title') }}</h3>
        </div>
        <!-- /.box-header -->

        <!-- box-body -->
        <div class="box-body">
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div :class="{'form-group': true, 'has-error': errors.has('project_name')}">
                                <label for="project_name" class="col-sm-2 control-label">{{trans('project.list_project.label_name')}}</label>
                                <div class="col-sm-10">
                                    <input type="text" v-validate="'required'" name="project_name" v-model="project.name" :class="{'input': true, 'form-control': true, 'is-danger': errors.has('project_name') }" id="project_name" placeholder="">
                                    <span v-show="errors.has('project_name')" class="help-block">{{ trans('project.message.required_project_name')}}</span>
                                </div>
                            </div>

                            <div :class="{'form-group': true, 'has-error': planCompareError != ''}">
                                <label for="project_name" class="col-sm-2 control-label">{{trans('project.create_project.plantime')}}</label>
                                <div class="col-sm-5">
                                    <date-picker-box @update-date="setPlanFrom" :language="local" :defaultDate="plan_start_time" classArr="form-control"></date-picker-box>
                                    <span v-show="planCompareError" class="help-block">{{ planCompareError }}</span>
                                </div>
                                <div class="col-sm-5">
                                    <date-picker-box @update-date="setPlanTo" :language="local" :startDate="plan_start_time" :defaultDate="plan_end_time" classArr="form-control"></date-picker-box>
                                </div>
                            </div>

                            <div :class="{'form-group': true, 'has-error': actualCompareError != ''}">
                                <label for="project_name" class="col-sm-2 control-label">{{trans('project.create_project.actualtime')}}</label>
                                <div class="col-sm-5">
                                    <date-picker-box @update-date="setActualFrom" :language="local" :defaultDate="actual_start_time" classArr="form-control"></date-picker-box>
                                    <span v-show="actualCompareError" class="help-block">{{ actualCompareError }}</span>
                                </div>
                                <div class="col-sm-5">
                                    <date-picker-box @update-date="setActualTo" :language="local" :startDate="actual_start_time" classArr="form-control" :defaultDate="actual_end_time"></date-picker-box>
                                </div>
                            </div>

                            <div :class="{'form-group': true}">
                                <label for="client_name" class="col-sm-2 control-label">{{trans('project.list_project.label_customer_name')}}</label>
                                <div class="col-sm-10">
                                    <select2 v-if="clients.length > 0" :data="clients" @getValue="setClientId" :current_value="project.client_id" :default_option="trans('project.list_project.label_client_choose')"></select2>
                                    <select2 v-if="clients.length <= 0" :data="clients" :default_option="trans('project.list_project.label_client_choose')"></select2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- box-body -->

        <!-- box-footer -->
        <div class="box-footer">
            <a href="/projectManager">
                <button type="button" class="btn btn-default">{{trans('project.list_project.label_dialog_close')}}</button>
            </a>
            <button type="btn btn-info pull-right" v-on:click="updateProject({'name': project.name, 'plan_start_time': plan_start_time, 'plan_end_time': plan_end_time, 'actual_start_time': actual_start_time,'actual_end_time': actual_end_time, '_token': token, 'id_project_resource': 1, 'id' : project.id, 'client_id': parseInt(client_id)})" class="btn btn-primary">{{trans('project.create_project.label_button_save')}}</button>
        </div>
        <!-- box-footer -->
    </div>
</template>
<script>
import moment from 'moment';
export default{
    props:['project','local', 'clients'],
    data () {
        return {
            name: '',
            token : $('meta[name=csrf-token]').attr('content'),
            plan_start_time: this.project.plan_start_time != null ? this.local === 'vi' ? moment(this.project.plan_start_time).format("DD-MM-YYYY") : moment(this.project.plan_start_time).format("YYYY-MM-DD") : '',
            plan_end_time: this.project.plan_end_time != null ? this.local === 'vi' ? moment(this.project.plan_end_time).format("DD-MM-YYYY") : moment(this.project.plan_end_time).format("YYYY-MM-DD") : '',
            actual_start_time: this.project.actual_start_time != null ? this.local === 'vi' ? moment(this.project.actual_start_time).format("DD-MM-YYYY") : moment(this.project.actual_start_time).format("YYYY-MM-DD") : '',
            actual_end_time: this.project.actual_end_time != null ? this.local === 'vi' ? moment(this.project.actual_end_time).format("DD-MM-YYYY") : moment(this.project.actual_end_time).format("YYYY-MM-DD") : '',
            client_id: this.project.client_id != null ? this.project.client_id : '',
            planCompareError: '',
            actualCompareError: ''
        }
    },
    methods: {
        setPlanFrom: function(value){
            this.plan_start_time = value;
        },
        setPlanTo: function(value){
            this.plan_end_time = value;
        },
        setActualFrom: function(value){
            this.actual_start_time = value;
        },
        setActualTo: function(value){
            this.actual_end_time = value;
        },
        setClientId: function(value){
            this.client_id = value;
        },
        updateProject: function(data){
            var _self = this;
            this.$validator.validateAll().then(result => {	    	
                if(result) {
                    if(!_self.compareDate(_self.plan_start_time, _self.plan_end_time)){
                        _self.planCompareError = this.trans('project.message.date_plan_equal_error');
                    }
                    else
                    {
                        _self.planCompareError = ''
                    }
                    
                    if(!_self.compareDate(_self.actual_start_time, _self.actual_end_time)){
                        _self.actualCompareError = this.trans('project.message.date_actual_equal_error');
                    }
                    else
                    {
                        _self.actualCompareError = ''
                    }

                    if(_self.planCompareError === '' && _self.actualCompareError === '')
                    {
                        this.$http.post('/projectManager/update', data).then( (response) => {
                            if(response.status === 200){
                                location.href = '/projectManager/'; 
                            }else{
                                this.showMessage(this.trans('project.list_project.label_dialog_title'), response.statusText, 'danger');
                            }
                        }, (response) => {
                            this.showMessage(this.trans('project.list_project.label_dialog_title'), response.status + ' ' +response.statusText, 'danger');
                            return false;
                        });
                    }
                }
            });
        },
        compareDate: function(date1, date2){
           
            var dateOne = this.local === 'vi' ? date1.split("-").reverse().join("-") : date1; 
            var dateTwo = this.local === 'vi' ? date2.split("-").reverse().join("-") : date2;
             
            if(dateOne != '' && dateTwo != '')
            {
                return dateOne <= dateTwo;
            }
            return true;
        }
    },
}    
</script>