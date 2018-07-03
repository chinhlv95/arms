<template>
    <div class="box box-primary">
        <!-- Loading (remove the following to stop the loading)-->
	    <div class="overlay">
	      	<i class="fa fa-refresh fa-spin"></i>
	    </div>
	    <!-- end loading -->

        <!-- box-header -->
        <div class="box-header  with-border">
            <h3 class="box-title">{{trans('project.menu.list')}}</h3>
        </div>
        <!-- /.box-header -->

        <!-- box-body -->
        <div class="box-body">
            <div id="project_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <!--search, show entries-->
                <div class="row">                    
                    <!--search-->
                    <div class="col-lg-6">
                        <div id="example1_filter" class="dataTables_length text-left">
                            <label>{{trans('project.list_project.label_search')}}
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-search"></i></div>
                                    <input type="text" class="form-control" data-mask="" data-vv-delay="1000" :placeholder="trans('project.list_project.label_search') + '...'" v-on:keyup="getListProject(key)" v-model="key">
                                </div>
                            </label>
                        </div>
                    </div>
                    <!--search-->

                    <!--Create-->
                    <div class="col-lg-6 text-right">
                        <div class="dataTables_length" id="example1_length">
							<button type="button" data-toggle="modal" data-target="#mappingProject" class="btn btn-warning btn-md" > {{ trans('project.list_project.label_project_mapping') }}</button>

                            <a href="/projectManager/create">
                                <button type="button" class="btn btn-info">{{ trans('project.list_project.label_create') }}</button>
                            </a>
                            <button type="button" class="btn btn-success btn-md" v-on:click="synchData"><i class="fa fa-refresh" aria-hidden="true" ></i> {{ trans('project.list_project.label_sync') }}</button>
                        </div>
                    </div>
                    <!--Create-->
                </div>
                <!-- table show list user -->
                <div class="row">
                    <div class="col-sm-12">
                        <table id="data_list" class="table table-bordered table-striped" v-if="projects.length">
                            <thead>
                            <tr>
                                <th width="3%">{{ trans('project.list_project.label_no') }}</th>
								<th width="20%">{{ trans('project.list_project.label_customer_name') }}</th>
                                <th>{{ trans('project.list_project.label_name') }}</th>
                                <th width="50%">{{ trans('project.list_project.label_project_japan') }}</th>
                                <th width="10%">{{ trans('project.list_project.label_action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, key) in projects">
                                <td>{{ key + 1 }}</td>
								<td>{{ item.client_name }}</td>
                                <td>{{ item.project_name }}</td>
                                <td>
									<span style="display: block" v-if="item['projects_jp'].length > 0" v-for="project_jp in item['projects_jp']" :title="item['projects_jp'].name">{{project_jp.name}}</span><br/>
									<a href="#" v-on:click.prevent="tracking_current_project(item.project_id)">{{ current_selected_project.indexOf(item.project_id) > -1 ? trans('project.list_project.label_assigned_member_collapsed') : trans('project.list_project.label_assigned_member_expanded') }}</a>
									<div class="member_list">
										<span style="display: inline-block" v-if="item['members'].length > 0 && current_selected_project.indexOf(item.project_id) > -1" v-for="member in item['members']" :title="member.fullname">
											<a href="#" data-toggle="modal" data-target="#modal-delete-member" v-on:click="saveInfoMemberRemoved(item.project_id, member.id)"><i class="fa fa-fw fa-remove"></i></a>
											<span>{{ member.username }}</span> &nbsp;
										</span>
									</div>
                                </td>
                                <td class="action">
                                    <a v-bind:href="'/projectManager/edit/' + item.project_id">{{ trans('project.list_project.label_edit') }}</a>
                                    <a href="#" data-toggle="modal" data-target="#modal-delete" v-on:click.prevent="deleteId = item.project_id">{{ trans('project.list_project.label_delete') }}</a>
                                    <a href="#" data-toggle="modal" data-target="#assignMember" v-on:click.prevent="assignProject(item.project_id)">{{ trans('project.list_project.label_assign') }}</a>

                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>{{trans('project.list_project.label_no')}}</th>
								<th>{{ trans('project.list_project.label_customer_name') }}</th>
                                <th>{{trans('project.list_project.label_name')}}</th>
                                <th>{{ trans('project.list_project.label_project_japan') }}</th>
                                <th>{{ trans('project.list_project.label_action') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->

        <div id="modal-delete" class="modal fade">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                <span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title">{{trans('project.list_project.label_dialog_delete_title')}}</h4>
			        </div>
			        <div class="modal-body">
			            <p>{{trans('project.list_project.label_delete_item')}}</p>
			        </div>
			        <div class="modal-footer">
				        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('project.list_project.label_dialog_close')}}</button>
				        <button type="button" class="btn btn-primary" v-on:click="deleteProject(deleteId)">{{trans('project.list_project.label_delete')}}</button>
			        </div>
			    </div>
			    <!-- /.modal-content -->
		    </div>
		    <!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- modal delete member-->
        <div id="modal-delete-member" class="modal fade">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                <span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title">{{ trans('project.list_project.label_delete_member')}}</h4>
			        </div>
			        <div :class="{'modal-body': true}">
                        <p>{{ trans('project.list_project.label_delete_item')}}</p>
			        </div>
			        <div class="modal-footer">
				        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('project.list_project.label_dialog_close')}}</button>
				        <button type="button" class="btn btn-primary" v-on:click="removeMemberAssigned">{{trans('project.list_project.label_delete')}}</button>
			        </div>
			    </div>
			    <!-- /.modal-content -->
		    </div>
		    <!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- modal assign members-->
        <div id="assignMember" class="modal fade" role="dialog">
	        <div class="modal-dialog">
	
	            <!-- Modal content-->
	            <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal">&times;</button>
	                <h4 class="modal-title">{{ trans('project.list_project.label_project_choose') }}</h4>
	            </div>
	            <div class="modal-body">
	                <div class="input-group">
	                    <input type="text" name="project_name" v-model="search_member" class="form-control" data-vv-delay="1000" v-on:keyup="searchMemberAssign()" :placeholder="trans('project.list_project.label_search') + '...'">
	                    <span class="input-group-btn">
	                        <button type="button" name="search" id="search-btn" class="btn btn-flat">
	                            <i class="fa fa-search"></i>
	                        </button>
	                    </span>
	                </div><br/>
	                <div class="warrpper scrollbar" id="scr-gray">
	                    <div class="inner">
	                        <div class="form-group">
	                            <div class="checkbox" v-for="member in lst_member">
	                                <label><input type="checkbox" v-on:click="addAssignedMember(member.id)" :title=" member.fullname" :checked="checkAssigned(member.id)">{{member.member_code}}_{{ member.fullname }}</label>
	                            </div>
	                        </div>
	                    </div>            
	                </div>
	            </div>
	
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('project.list_project.label_dialog_close')}}</button>
			        <button type="button" class="btn btn-primary" v-on:click="saveAssignedInfo">{{trans('project.list_project.label_dialog_yes')}}</button>
	            </div>
	            </div>
	        </div>
        </div>
        <!-- /.modal -->

		<!-- modal mapping project -->
        <div id="mappingProject" class="modal fade" role="dialog">
	        <div class="modal-dialog modal-lg">
	
	            <!-- Modal content-->
	            <div class="modal-content">

				<!-- Loading (remove the following to stop the loading)-->
				<div class="overlay mappingLoading">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
				<!-- end loading -->

	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal">&times;</button>
	                <h4 class="modal-title">{{ trans('project.list_project.label_project_mapping') }}</h4>
	            </div>
	            <div class="modal-body">
					<label for="maeyes_project_import">{{ trans('project.list_project.label_upload_file') }}</label>
					<div class="form-group">
						<label><input type="file" v-validate="'ext:xlsx,xlsm,xls,xltx,xltm,XSLS,XLSM,XLS,XLTX,XLTM'" id="maeyes_project_import" name="maeyes_project_import"></label>
						<label><a href="#" v-on:click="importProject" class="btn btn-info">{{ trans('project.list_project.label_upload') }}</a></label>
						<span v-if="errors.has('maeyes_project_import') || messageError != '' " class="error help is-danger">{{ errors.has('maeyes_project_import') ? messageError = trans('project.message.import_format_error') : messageError}}</span>
                	</div>
					<label class="control-label help is-danger" v-if="collumnError"><a class="error" href="/projectManager/ExportLog">{{trans('project.message.has_collumn_error')}}</a></label>
					<table class="table table-bordered" v-if="maeyes_project.length > 0">
						<thead>
							<tr>
								<th style="width: 20%">{{ trans('project.list_project.label_customer_name') }}</th>
								<th>{{ trans('project.list_project.label_project_name') }}</th>
								<th style="width: 40%">{{ trans('project.list_project.label_client_choose') }}</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="item in maeyes_project">
								<td>{{item.client_name}}</td>
								<td>{{item.name}}</td>
								<td>
									<div class="form-group">
										<select2 v-if="arms_mapping_projects.length > 0" :data="arms_mapping_projects" :default_option="trans('project.list_project.label_arms_project_choose')"  @getValue="setProjectId" @on_change="settingProjectMapping(item.code)"></select2>
										<select2 v-if="arms_mapping_projects.length <= 0" :data="arms_mapping_projects" :default_option="trans('project.list_project.label_arms_project_choose')"  @getValue="setProjectId" @on_change="settingProjectMapping(item.code)"></select2>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="3" class="text-right"><button type="button" class="btn btn-info" v-on:click="mappingProject">{{trans('project.list_project.label_save')}}</button></td>
							</tr>
						</tbody>
			  		</table>
	            </div>
	
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{trans('project.list_project.label_dialog_close')}}</button>
	            </div>
	            </div>
	        </div>
        </div>
        <!-- modal mapping project -->
		
    </div>
</template>
<script>
    export default{
        props: ['entries', 'project_resource'],
        data(){
            return {
                projects: [], //projects array,
				arms_mapping_projects: [],
                limit: 10,
                key: '',
                deleteId: 0,
                token : $('meta[name=csrf-token]').attr('content'),
                id_project_resource: 2,
                messageError: '',
                search_member: '',// key search member
                lst_member: '',
                update_assign: {'id_project': null, 'list_member': []},
                member_assigned: {'id_project': null, 'id_user': null},
				maeyes_project: [],
				arms_project_id: 0,
				collumnError: false,
				current_selected_project: []
            }
        },
        mounted: function() {
			var _self = this;

            this.getListProject(this.key);

			$(document).on('hidden.bs.modal', '#mappingProject', function(e){
				_self.maeyes_project = [];
				_self.messageError = '';
				$('input[name=maeyes_project_import]').val('');
				_self.collumnError = false;
				_self.$validator.clean();
			});
        },
        methods: {
			setProjectId: function(id){//set project id for mapping
				this.arms_project_id = id;
			},
        	saveInfoMemberRemoved: function(project_id, member_id){
        		this.member_assigned.id_project = project_id;
        		this.member_assigned.id_user = member_id;
        	},
        	removeMemberAssigned: function(){
        		$("#modal-delete-member").modal('hide');
        		this.member_assigned._token = this.token;
        		this.$http.post('/project/deleteMemberAssigned', this.member_assigned).then((response)=>{
        			if(response.status === 200){
        				this.getListProject(this.key);
        			}
        			else{
        				this.showMessage(this.trans('origanization.message.title_name'), response.body.message, 'danger');
        			}
        		}, (response)=>{
        			$('body,html').animate({
                        scrollTop: 0
                    }, 100);
        			this.showMessage(this.trans('origanization.message.title_name'), response.status + ' ' +response.statusText, 'danger');
        		});
        	},
        	saveAssignedInfo: function(){
        		$("#assignMember").modal('hide');
        		this.update_assign._token = this.token;

        		this.$http.post('/project/saveAssign', this.update_assign).then((response)=>{
        			if(response.status === 200){
        				this.getListProject(this.key);
        			}
        			else{
        				this.showMessage(this.trans('origanization.message.title_name'), response.body.message, 'danger');
        			}
        		}, (response)=>{
        			$('body,html').animate({
                        scrollTop: 0
                    }, 100);
        			this.showMessage(this.trans('origanization.message.title_name'), response.status + ' ' +response.statusText, 'danger');
        		});
        	},
        	addAssignedMember: function(id){ 
        		if(this.update_assign.list_member.indexOf(id) >= 0){
        			//remove
        			this.update_assign.list_member.splice(this.update_assign.list_member.indexOf(id), 1);
        		}
        		else//add
        			this.update_assign.list_member.push(id);
        	},
        	assignProject: function(id){
        		this.search_member = '';
        		this.getListMember();
        		this.update_assign.id_project = id;
        		this.update_assign.list_member = [];

        		for(var i = 0; i< this.projects.length; i++){
        			if(this.projects[i].project_id == id){
        				if(this.projects[i].members.length > 0){
							
        					for(var j = 0; j < this.projects[i].members.length; j++){
        						this.update_assign.list_member.push(this.projects[i].members[j].id);
        					}
        				}
        				break;
        			}
        		}
        	},
        	checkAssigned: function(member){
        		if(this.update_assign.list_member.indexOf(member) >= 0){
        			return true;
        		}
        		else
        			return false;
        	},
        	// search member name/member_code in model assign 
        	searchMemberAssign: function(){
        		this.getListMember();
        	},
        	getListMemberWithKey: function(key){
				this.$http.get('/user/search/' + key).then((response)=>{
					if(response.status === 200)
					{
						this.lst_member = response.data;
					}
				}, (response)=>{
					return false;
				});
			},
        	//get list member to show in model assign
        	getListMember: function(){
        		if(this.search_member.length > 0){
        			this.getListMemberWithKey(this.search_member);
        		}
        		else{
        			this.$http.get('/user/get').then((response)=>{
    					if(response.status === 200)
    					{
    						this.lst_member = _.uniqBy(response.data.active, 'id');
    					} 
    				}, (response)=>{
    					return false;
    				});
        		}
        	},
            // get project list
            getListProject: function (){
				var _self = this;
                this.$http.get('/projectManager/list?key=' + this.key).then( (response) => {
                    if(response.body.length > 0){
                        this.projects = response.body;
						//create array arms projects for mapping
						if(this.projects.length > 0){
							this.projects.map(function(e){
								_self.arms_mapping_projects.push({'id': e.project_id, 'name': e.project_name});
							});
						}
                    }else{
                        this.projects = [];
                    }
                }, (response) => {
                    return false;
                });
            },
            //delete project
            deleteProject: function(id){
				var index = _.findIndex(this.projects, function(o){
					return o.project_id == id && o.members.length > 0;
				});
				
				if(index >= 0)
				{
					$("#modal-delete").modal('hide');
					this.showMessage(this.trans('project.list_project.label_dialog_title'), this.trans('project.message.error_foreign_delete'), 'danger');
				}else{
					this.$http.post('/projectManager/delete', {'project_id': id, '_token': this.token}).then( (response) => {
						if(response.status === 200 && response.body === 'success'){
							$("#modal-delete").modal('hide');
							this.getListProject();
							this.showMessage(this.trans('project.list_project.label_dialog_title'), this.trans('project.list_project.label_delete_success'), 'success');
						}
					}, (response) => {
						$("#modal-delete").modal('hide');
						this.showMessage(this.trans('project.list_project.label_dialog_title'), response.status + ' ' +response.statusText, 'danger');
						return false;
					});
				}
            },
            synchData: function(){
				$('.box-primary .overlay').css('display','block');
				this.$http.get('/project/synchData').then((response)=>{
					
					if(response.status === 200)
					{
						location.reload(true);
					}
					else{
						$('.box-primary .overlay').css('display','none');
						this.showMessage(this.trans('project.list_project.label_dialog_title'), response.statusText, 'danger');
					}
				},(response)=>{
					$('.box-primary .overlay').css('display','none');
					this.showMessage(this.trans('project.list_project.label_dialog_title'), response.status + ' ' +response.statusText, 'danger');
				});
			},
			settingProjectMapping: function(maeyes_project_code){//push selected project ( project_id ) for array mapping (maeyes_project)
				var index = _.findIndex(this.maeyes_project, function(o){
					return o.code == maeyes_project_code;
				});

				if(typeof index != 'undefined')
				{
					this.maeyes_project[index].arms_project_id = this.arms_project_id;
				}
			},
			importProject: function(){
				this.$validator.validateAll().then(result => {
                    if(result) {
						var formData = new FormData();
						var _self = this;

						if(typeof $('#maeyes_project_import')[0].files[0] === 'undefined')
						{
							this.messageError = this.trans('project.message.import_file_required');
						}else{
							this.messageError = '';

							formData.append('importfile', $('#maeyes_project_import')[0].files[0]);
							formData.append('_token', this.token);

							this.$http.post('/projectManager/import', formData, {
								progress (e) {
									_self.maeyes_project = [];
									$('.mappingLoading').css('display', 'block');
								}
							}).then( (response) => {
								$('.mappingLoading').css('display', 'none');

								if(response.body !== 'TmpInvalid')
								{
									//export maeyes_project
									response.body.map(function(e,i){

										_self.maeyes_project.push({
											'code': e.プロジェクトコード, 
											'name' : e.プロジェクト名, 
											'type' : e.プロジェクト区分, 
											'client_code' : typeof e.得意先コード != 'undefined' && e.得意先コード != null && e.得意先コード.toString().length <= 4 ? parseInt(e.得意先コード) : null,  
											'client_name' : typeof e.得意先コード != 'undefined' && e.得意先コード != null ? e.得意先名称 : null, 
											'order_date' : e.受注日, 
											'start_date' : e.着手日, 
											'delivery_date' : e.納品日, 
											'acceptance_date' : e.検収予定日, 
											'plan_completion_date' : e.完了予定日 , 
											'chief_staff' : e.主任担当者,
											'arms_project_id': 0
										});
									});
																		
									_.remove(_self.maeyes_project, function(e){
										return e.code == null;
									});

									_self.checkHasErrorCollumn(response.body);
									$('input[name=maeyes_project_import]').val('');
								}
								else{
									$('.mappingLoading').css('display', 'none');
									this.messageError = this.trans('project.message.import_template_error');
									$('input[name=maeyes_project_import]').val('');
								}
							}, (response) => {
								$('.mappingLoading').css('display', 'none');
								$('input[name=maeyes_project_import]').val('');

								this.messageError = response.status + ' ' +response.statusText;
								return false;
							});
						}
					}
				});
			},
			mappingProject: function(){
				$('#mappingProject').modal('hide');
				
				this.$http.post('/projectManager/mapping', {'data': this.maeyes_project, '_token': this.token}).then((response) => {
					if(response.body === 'sucess')
					{
						this.getListProject();
						this.showMessage(this.trans('project.list_project.label_dialog_title'), this.trans('project.list_project.label_project_mapping_success'), 'success');
					}else{
						this.showMessage(this.trans('project.list_project.label_dialog_title'), this.trans('project.list_project.label_project_mapping_error'), 'danger');
					}
				}, (response) => {
					this.showMessage(this.trans('project.list_project.label_dialog_title'), response.status + ' ' +response.statusText, 'danger');
				});
			},
			checkHasErrorCollumn: function(array){//check has error column in imported project array
				
				if(array.length > 0)
				{
					var findError = _.findKey(array, function(o) { 
						return o.has_error; 
					});
					
					return typeof findError != 'undefined' ? this.collumnError = true : this.collumnError = false;
				}
				return false;
			},
			tracking_current_project: function(project_id){
				var _self = this;
				this.getListMember();

				if(this.current_selected_project.indexOf(project_id) != -1)
				{
					this.current_selected_project.map(function(e, i){
						
						if(e == project_id)
						{
							_self.current_selected_project.splice(i, 1);
						}
					});
				}else{
					this.current_selected_project.push(project_id);
				}
			}
        }
    }
</script>