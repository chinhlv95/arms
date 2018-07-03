<template>
	<div class="box box-primary">
		<!-- /.box-header -->
		<div class="box-body">
			<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
				<!--search, show entries-->
				<div class="row">
					<!--search-->
					<div class="col-sm-3 text-left">
						<div id="example1_filter"> 
							<label>{{ trans('user.label.label_search') }}: &nbsp;<input type="search" class="form-control input-sm" data-vv-delay="1000" :placeholder="trans('user.label.label_search') + '...'" aria-controls="example1" v-on:keyup="search" v-model="keySearch"></label>
						</div>
					</div>
					<div class="col-sm-9 text-right"><a href="#" data-toggle="modal" data-target="#show-member-deactive" v-on:click="addMemberToActive"><i class="fa fa-lock" aria-hidden="true"></i> {{trans('user.label.label_lock')}} ({{member_unlock.length}})</a></div>
				</div>
				
				<!-- table show list user -->
				<div class="row">
					<div class="col-sm-12">
						<table id="data_list" class="table table-bordered table-striped" v-if="items.length">
							<thead>
								<tr>
									<th width="3%">{{ trans('user.label.label_no') }}</th>
									<th>{{ trans('user.label.label_username') }}</th>
									<th>{{ trans('user.label.label_fullname') }}</th>
									<th>{{ trans('user.label.label_email') }}</th>
									<th>{{ trans('user.label.label_phone') }}</th>
									<th>{{ trans('user.label.label_skype') }}</th>
									<th>{{ trans('user.label.label_role') }}</th>
									<th width="9%">{{ trans('user.label.label_search_action') }}</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, key) in items">
									<td> {{ key + 1}} </td>
									<td>{{ item.username }}</td>
									<td>{{ item.fullname }}</td>
									<td>{{ item.email }}</td>
									<td v-if="item.phone">
										<span v-if="item.calling_code">{{ '[' + item.calling_code +'] '}}</span>
										{{ item.phone }}
									</td>
									<td v-else></td>
									<td>{{ item.skype }}</td>
									<td v-if="item.roles.length > 0">
										<strong v-for="(role,key) in item.roles">
											<span v-if="key < (item.roles.length -1)">
												{{ role.role_name }} &nbsp;|
											</span>
											<span v-else>
											&nbsp;{{ role.role_name }}
											</span>
										</strong>
									</td>
									<td v-else>
										<span>Member</span>
									</td>
									<td class="action">
										<a v-bind:href="'/user/update/' + item.id">{{ trans('user.label.label_edit_link') }}</a>
										<a href="#" data-toggle="modal" data-target="#delete-item" v-on:click.prevent="getModelDelete(item.id)">{{ trans('user.label.label_delete_link') }}</a>
										<a href="#" data-toggle="modal" v-if="auth != item.id && !checkRole('Admin', item.roles)" data-target="#add-role" v-on:click.prevent="getModelAddRole(item.id, item.roles)">{{ trans('user.label.label_role_link') }}</a>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th>{{ trans('user.label.label_no') }}</th>
									<th>{{ trans('user.label.label_username') }}</th>
									<th>{{ trans('user.label.label_fullname') }}</th>
									<th>{{ trans('user.label.label_email') }}</th>
									<th>{{ trans('user.label.label_phone') }}</th>
									<th>{{ trans('user.label.label_skype') }}</th>
									<th>{{ trans('user.label.label_role') }}</th>
									<th>{{ trans('user.label.label_search_action') }}</th>
								</tr>
							</tfoot>
						</table>
						<!--paginate-->
					</div>
				</div>
			</div>
		</div>
		<!-- /.box-body -->
		<!-- modal add role user-->
        <div id="add-role" class="modal fade">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                <span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title">{{trans('user.label.label_dialog_add_role_title')}}</h4>
			        </div>
			        <div class="modal-body">
				        <div class="inner">
		                    <div class="form-group">
		                        <div class="checkbox" id="role-popup" v-for="item in roles">
		                            <label><input class="checkbox-role" type="checkbox" :value="item.id">{{item.role_name}}</label>
		                        </div>
		                    </div>
		                </div> 
			        </div>
			        <div class="modal-footer">
				        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('user.label.label_dialog_close')}}</button>
                        <button type="button" class="btn btn-primary" v-on:click="saveRole(idUpdateRole)">{{trans('user.label.label_dialog_save')}}</button>
			        </div>
			    </div>
			    <!-- /.modal-content -->
		    </div>
		    <!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!--model delete user-->
		<div id="delete-item" class="modal fade">
		    <div class="modal-dialog">
			    <div class="modal-content">
			        <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                <span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title">{{ trans('user.title_danger') }}</h4>
			        </div>
			        <div class="modal-body">
			            <p>{{ trans('user.label.label_delete_item') }}</p>
			            <p v-if="deletedItem === auth">{{ trans('user.label.label_logged_delete_item') }}</p>
			        </div>
			        <div class="modal-footer">
				        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('user.label.label_close') }}</button>
				        <button type="button" class="btn btn-primary" v-on:click.prevent="softDelete(deletedItem)">{{ trans('user.label.label_yes') }}</button>
			        </div>
			    </div>
			    <!-- /.modal-content -->
		    </div>
		    <!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<!-- model notification delete item-->
		<div class="modal fade" id="delete-notification">
	        <div class="modal-dialog">
	            <div class="modal-content">
		            <div class="modal-header">
		              	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                	<span aria-hidden="true">&times;</span></button>
		                <h4 class="modal-title">{{ trans('user.title_danger') }}</h4>
		            </div>
		            <div class="modal-body">
			            <div class="form-group has-success" v-if="flagDelete = 1">
		                  	<label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> {{ trans('user.delete_success') }}</label>
		                </div>
		                <div class="form-group has-error" v-if="flagDelete = 0">
		                  	<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ trans('user.delete_error') }}</label>
		                </div>
		            </div>
		            <div class="modal-footer">
		              	<button type="button" class="btn btn-default" data-dismiss="modal" v-on:click="closeMessage(deletedItem)">{{ trans('user.label.label_close') }}</button>
		            </div>
	            </div>
	            <!-- /.modal-content -->
	        </div>
	        <!-- /.modal-dialog -->
	    </div>
	    <!--end model notification-->
	    <!--model show member deactive-->
	    <div id="show-member-deactive" class="modal fade" role="dialog">
	        <div class="modal-dialog">
	            <!-- Modal content-->
	            <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal">&times;</button>
	                <h4 class="modal-title"> {{ trans('user.label.label_member_block') }}</h4>
	            </div>
	            <div class="modal-body">
	                <div class="input-group">
	                    <input type="text" v-model="keySearchMemberDeactive" class="form-control" data-vv-delay="1000" v-on:keyup="searchMemberDeactive()" :placeholder="trans('project.list_project.label_search') + '...'">
	                    <span class="input-group-btn">
	                        <button type="button" name="search" id="search-btn" class="btn btn-flat">
	                            <i class="fa fa-search"></i>
	                        </button>
	                    </span>
	                </div><br/>
	                <div class="warrpper scrollbar" id="scr-gray">
	                    <div class="inner">
	                        <div class="form-group">
	                            <!--<div class="checkbox" v-for="member in lst_member_deactive">
	                                <label><input type="checkbox" v-on:click="reactiveMember(member.id)" :title=" member.fullname" :checked="checkCheckboxStatus(member.id)">{{member.member_code}}_{{ member.fullname }}</label>
	                            </div>
	                            -->
	                            
	                            <table id="data_list" class="table table-bordered table-striped" v-if="items.length">
	    							<thead>
	    								<tr>
	    									<th>{{ trans('user.label.label_username') }}</th>
	    									<th width="25%">{{ trans('user.label.label_status') }}</th>
	    									<th width="15%"><label><input type="checkbox" v-on:click="selectAllMemberInactive"> {{ trans('user.label.label_all') }}</label></th>
	    								</tr>
	    							</thead>
	    							<tbody>
	    								<tr v-for="member in lst_member_deactive">
	    									<td>{{member.member_code}}_{{ member.fullname }}</td>
	    									<td>
	    										<a href="#" v-if="checkLock(member.id)" v-on:click="reactiveMember(member.id)"><i class="fa fa-unlock" aria-hidden="true"></i> {{trans('user.label.label_lock')}}</a>
	    										<a href="#" v-if="!checkLock(member.id)" v-on:click="reactiveMember(member.id)"><i class="fa fa-lock" aria-hidden="true"></i> {{trans('user.label.label_unlock')}}</a>
	    									</td>
	    									<td>
	    									<label><input type="checkbox" v-on:click="reactiveMember(member.id)" :title=" member.fullname" :checked="checkLock(member.id)"></label>
	    									</td>
	    								</tr>
	    							</tbody>
	    						</table>
	                        </div>
	                    </div>            
	                </div>
	            </div>
	
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('project.list_project.label_dialog_close')}}</button>
			        <button type="button" class="btn btn-primary" v-on:click="activeMember">{{trans('project.list_project.label_dialog_yes')}}</button>
	            </div>
	            </div>
	        </div>
	    </div>
	    <!--/ end model show member deactive-->
    
    
	</div>
</template>
<script>
	export default {
		props: {
			numberentry: '',
			resource: '',
			auth: {
				type: Number,
				default: function(){
					return 0;
				}
			}
		},
		data() {
			return {
				items: '',
				deletedItem: '',
				token : $('meta[name=csrf-token]').attr('content'),
			    per_page: 10, // show entries per page default is 10
			    keySearch: '',
			    flagDelete: 0,
			    roles: '',
			    roleSelected: [],
			    idUpdateRole: null,
			    token : $('meta[name=csrf-token]').attr('content'),
			    lst_member_deactive: [], // contain members soft deleted
			    lst_member_reactive: [],
			    keySearchMemberDeactive: '',
			    checkSelectAllMemberInactive: false, 
			    member_unlock: []
			}
		},
		created: function() {
			this.getAll(); // get all user
			this.getRole(); // get all role
		},
		methods: {
			selectAllMemberInactive: function(){ // get id of all member was lock
				if(!this.checkSelectAllMemberInactive){
					this.lst_member_reactive = _.map(this.lst_member_deactive, 'id');
					this.checkSelectAllMemberInactive = true;
				}
				else{
					this.lst_member_reactive = [];
					this.checkSelectAllMemberInactive = false;
				}
			},
			searchMemberDeactive: function(){ // search members was lock in popup model
				if(this.keySearchMemberDeactive.length > 0){
					var result = [];
					var key = this.keySearchMemberDeactive.toLowerCase();
					for(var i = 0; i < this.member_unlock.length; i++){
						var username = this.member_unlock[i].username ? this.member_unlock[i].username.toLowerCase() : '';
						var fullname = this.member_unlock[i].fullname ? this.member_unlock[i].fullname.toLowerCase() : '';
						var member_code = this.member_unlock[i].member_code ? this.member_unlock[i].member_code.toLowerCase() : '';
						if(username.indexOf(key) != -1 || member_code.indexOf(key) != -1 || fullname.indexOf(key) != -1){
							result.push(this.member_unlock[i]);
						}
					}
					this.lst_member_deactive = result;
				}
				else{
					this.lst_member_deactive = this.member_unlock;
				}
			},
			addMemberToActive: function(){ // reset list member active when show popup
				this.lst_member_reactive = [];
			},
			activeMember: function(){// send requets to server and active account was lock
				if(this.lst_member_reactive.length > 0){
					this.$http.post('/user/active', {'_token': this.token, 'account': this.lst_member_reactive}).then((response)=>{
						if(response.status === 200){
							$("#show-member-deactive").modal('hide');
							this.getAll();
							this.showMessage(this.trans('user.title_danger'), this.trans('user.messages.success_active'), 'success');
						}
					}, (response)=>{
						this.showMessage(this.trans('user.title_danger'), response.body.message, 'danger');
					});
				}
			},
			reactiveMember: function(id){ // check and add member to active/unlock
				if(this.lst_member_reactive.indexOf(id) == -1){
					this.lst_member_reactive.push(parseInt(id));
				}
				else{
					this.lst_member_reactive.splice(this.lst_member_reactive.indexOf(id), 1);
				}
			},
        	checkLock: function(member){ // check checked status to add attribute checked in input checkbox tag
        		if(this.lst_member_reactive.indexOf(member) >= 0){
        			return true;
        		}
        		else
        			return false;
        	},
			getAll: function(){
				this.$http.get('/user/get').then((response)=>{
					if(response.status === 200)
					{ 	
						this.lst_member_deactive = response.data.deactive;
						this.member_unlock = response.data.deactive;
						this.items  = this.convertArr(response.data.active);
					} 
				}, (response)=>{
					this.showMessage(this.trans('user.title_danger'), response.body.message, 'danger');
				});
			},
			convertArr: function(data){
				var arr = [];
				if(data.length > 0){
					
					for(var i = 0; i < data.length; i++){
						var check = false;
						for(var j = 0; j < arr.length; j++){
							if(arr[j].id == data[i].id){
								check = j;
							}
						}
						if(typeof(check) === 'boolean'){
							var user = {
									'id' : data[i].id,
									'username' : data[i].username,
									'fullname' : data[i].fullname,
									'email' : data[i].email,
									'phone' : data[i].phone,
									'calling_code' : data[i].calling_code,
									'skype' : data[i].skype,
									'roles' : []
							};
							if(data[i].role_id){
								user.roles.push({
									'role_id': data[i].role_id,
									'role_name': data[i].role_name
								})
							}
							arr.push(user);
						}
						else if(typeof(check) === 'number'){
							arr[check].roles.push({
								'role_id': data[i].role_id,
								'role_name': data[i].role_name
							})
						}
					}
				}
				return arr;
			},
			// get list with key search
			getSearch: function(key){
				this.$http.get('/user/search/' + key).then((response)=>{
					if(response.status === 200)
					{
						this.items  = this.convertArr(response.data);
					}
				}, (response)=>{
					this.showMessage(this.trans('user.title_danger'), response.body.message, 'danger');
				});
			},
			// show model when click action delete user
			getModelDelete: function(id){
				this.deletedItem = id;
			},
			// delete user
			softDelete: function(id)
			{
				this.$http.post('/user/delete',{ 'id':id, '_token': this.token }).then((response)=>{
					if(response.status === 200)
					{
						$("#delete-item").modal('hide');
						this.flagDelete = 1; // delete success
						$("#delete-notification").modal('show');
						
						this.search();
					} 
				}, (response) =>{
					this.flagDelete = 0;
					$("#delete-notification").modal('show');
					this.search();
				});				
			},
			closeMessage: function(id){
				// if account login is deleted, browse will be reloaded and user must login again with other account
				if(id === this.auth){
					location.reload(true);
				}
				$("#delete-notification").modal('hide');
			},
	        // if user input keysearch, function search will be called
		    search: function(){
		    	if(this.keySearch.length > 0)
	        	{
		    		this.getSearch(this.keySearch);
	        	}
	        	else
	        	{
	        		this.getAll();
	        	}
		    },
			//add role
		    getModelAddRole: function(id, data){
		    	this.idUpdateRole = id;
		    	var arr = [];
		    	for(var i = 0; i < data.length; i++){
		    		arr.push(parseInt(data[i].role_id))
		    	} 
		    	var result = document.getElementsByClassName("checkbox-role");
		    	for(var i = 0; i < result.length; i++){
		    		var index = arr.indexOf(parseInt(result[i].defaultValue))
		    		if(index >= 0){
		    			result[i].checked = true;
		    		}
		    		else
		    			result[i].checked = false;
		    	}
		    },
		    getRole: function(){
		    	this.$http.get('/role/getAll').then((response)=>{
		    		if(response.status === 200){
		    			this.roles = response.data;
		    		}
		    	}, (response)=>{
		    		this.showMessage(this.trans('user.title_danger'), response.body.message, 'danger');
		    	});
		    },
		    saveRole: function(id){
		    	var result = document.getElementsByClassName("checkbox-role");
		    	var arrData = [];
		    	if(result.length > 0){
		    		for(var i = 0; i < result.length; i++){
			    		if(result[i].checked == true){
			    			arrData.push(parseInt(result[i].defaultValue));
			    		}
			    	}
		    	}
		    	// update role
		    	var data = {
		    			'_token' : this.token,
		    			'id' : id,
		    			'data' : arrData
		    	};
		    	this.$http.post('/role/update', data ).then((response)=>{
		    		if(response.status === 200){
		    			$("#add-role").modal('hide');
		    			this.search();
		    		}
		    	}, (response)=>{
		    		this.showMessage(this.trans('user.title_danger'), response.body.message, 'danger');
		    	});
		    	
		    },
			checkRole: function(role ,roles){
				if(roles.length > 0)
				{
					var check = _.findIndex(roles, function(o){
						return o.name == role;
					});

					if(check != -1)
					{
						return true;
					}
					return false;
				}
				return false;
			}
		}
	}
</script>