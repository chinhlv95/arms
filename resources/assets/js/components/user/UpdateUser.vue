<template>
	<div class="row">
		<div class="col-md-3">
			<!-- Profile Image -->
			<div class="box box-primary">
				<div class="box-body box-profile">
					<div class="preview-image">
						<img class="profile-user-img img-responsive img-circle" :src="editUser.avatar != null ? '/upload/avatar/' + editUser.avatar : '/images/avatar_default.png'"  alt="User profile picture">
					</div>

				    <h3 class="profile-username text-center">{{editUser.username}}</h3>
				
				    <p class="text-muted text-center">{{editUser.email}}</p>
				
				    <ul class="list-group list-group-unbordered">
				    	<!--change image profile-->
					    <li :class="{'list-group-item': true, 'form-group': true, 'has-error': errors.has('profileImage')}" >
					    	<label for="profileImage"><a>{{trans('user.label.label_edit_avatar')}}</a></label>
		                  	<input type="file" id="profileImage" v-validate="'mimes:image/jpeg,image/png,image/jpg,image/gif|size:2048'" name="profileImage" v-on:change="changeProfileImage" style="display: none">
		                  	<span v-show="errors.has('profileImage')" class="help-block">{{ trans('user.messages.upload_avatar_error') }}</span>
					    </li>
					    <!--change password-->
					    <li class="list-group-item" v-if="editUser.id_resource == 1">
					    	<label for="newPassword"><a class="text-center">{{trans('user.label.label_edit_password')}}</a></label>
					    	
					    	<div :class="{'form-group': true, 'has-error': errors.has('newPassword')}">
			                  	<input type="password" class="form-control" v-validate="'min:8|max:16'" id="newPassword" placeholder="Enter new password" name="newPassword" v-model="newPassword">
			                  	<span v-show="errors.has('newPassword')" class="help-block">{{ trans('user.messages.pass_length') }}</span>
			                </div>
					    </li>
				    </ul>
				    <button class="btn btn-primary btn-block" v-on:click="saveChangePassword" :disabled="errors.has('newPassword') == true || errors.has('profileImage') == true"> {{trans('user.label.label_save')}}</button>
				    
				</div>
				<!-- /.box-body -->
			</div>
			
			
		</div>
	    <div class="col-md-9">
	  		<div class="col-md-12">
	  		<!-- general form elements -->
	      		<div class="box box-primary">
	      			<div class="box-header with-border">
	      				<h3 class="box-title">{{ trans('user.title_detail') }}</h3>
	      			</div>
	      			<!-- /.box-header -->
	            	<!-- form start -->
	            	<form role="form" method="POST" enctype="multipart/form-data" id="formCreateuser">
	            		<div class="box-body">
	            			<!--username-->
	            			<div :class="{'form-group': true, 'has-error': errors.has('username') || errors.has('nameunique')}">
	            				<label for="username">{{trans('user.label.label_username')}}</label>
	            				<div class="input-group">
	            					<span class="input-group-addon">@</span>
	            					<input type="text" data-vv-delay="300" class="form-control" v-validate="'required|nameunique:1'" placeholder="Username" name="username" v-model="editUser.username" id="username">
	            				</div>
	            				<span v-show="errors.has('username') || errors.has('nameunique')" class="help-block">{{ errors.all().indexOf('The username field is required.') > -1 ? trans('user.messages.user_required') : trans('user.messages.user_already_existed') }}</span>
	            			</div>
	            			
			                <!--fullname-->
				            <div :class="{'form-group': true, 'has-error': errors.has('fullname')}">
				              	<label for="fullname">{{trans('user.label.label_fullname')}}</label> 
				              	<input type="text" data-vv-delay="500" class="form-control" id="fullname" placeholder="Full name" name="fullname" v-model="editUser.fullname">
				              	<span v-show="errors.has('fullname')" class="help-block">{{ errors.first('fullname') }}</span>
				            </div>
				            
				            <!--email-->
				            <div :class="{'form-group': true, 'has-error': errors.has('email')}">
				                <label for="email">{{trans('user.label.label_email')}}</label> 
					            <div class="input-group">
					                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
					                <input type="email" data-vv-delay="500" v-validate="'email|unique:1'" class="form-control" placeholder="Email" name="email" v-model="editUser.email" id="email">
					            </div>
					            <span v-show="errors.has('email') || errors.has('unique')" class="help-block">{{ errors.all().indexOf('The email field must be a valid email.') > -1 ? trans('user.messages.email_invalid') : trans('user.messages.email_already_existed') }}</span>
					        </div>
					        
					        <!--phone number-->
					        <div :class="{'form-group': true, 'has-error': errors.has('phone')}">
					        	<label for="phone">{{trans('user.label.label_phone')}}</label>
					        	<div class="row">
					        		<div class="col-md-2">
						                <select class="form-control select2" style="width: 100%;" v-model="editUser.calling_code">
							                <optgroup label="Calling code">
							                  	<option :value="key" v-for="(value, key) in callingcode">{{value}}</option>
						                  	</optgroup>
						                </select>
					        		</div>
					        		<div class="col-md-10">
						        		<div class="input-group">
							        		<div class="input-group-addon">
							        			<i class="fa fa-phone"></i>
							        		</div>
							        		<input id="phone" type="text" data-vv-delay="500" v-validate="'numeric'" class="form-control" placeholder="Phone number" data-inputmask="'mask': ['999-999-9999 [x99999]', '+099 99 99 9999[9]-9999']" data-mask name="phone" v-model="editUser.phone">
							        	</div>
							        	<span v-show="errors.has('phone')" class="help-block">{{ trans('user.messages.phone_format') }}</span>
					        		</div>
					        	</div>
					        </div>
					        
			                <!--skype-->
			                <div class="form-group">
			                  	<label for="skype">{{trans('user.label.label_skype')}}</label>
			                  	<input type="text" class="form-control" id="skype" name="skype" placeholder="Skype" v-model="editUser.skype">
			                </div>
			                
			            </div>
			            <!-- /.box-body -->
	
			            <div class="box-footer">
			                <button type="button" class="btn btn-default" v-on:click="cancleUpdate">{{trans('user.label.label_cancel')}}</button>
							<button type="button" class="btn btn-primary" v-on:click="updateUser(editUser.id)">{{trans('user.label.label_save')}}</button>
			            </div>
		            </form>
	            </div>
	        </div>
	    </div>
	</div>
</template>
<script>
	export default {
		data() {
			return {
				token : $('meta[name=csrf-token]').attr('content'),
				existedFlag : '',
				existedFlagName : '',
				editUser : {},
				newPassword: '',
				input: {},
				newProfile: '',
				flagCheckAvatar: 0
			}
		},
		props: {
			users: {
				type: Object,
				default: function(){
					return {};
				}
			},
			path: {
				type: String,
				default: function(){
					return '';
				}
			},
			callingcode: '',
			imagedefault: {
				type: String,
				default: function(){
					return '';
				}
			}
		},
		computed: {
			
		},
		created: function(){
			this.editUser = this.users;
			
			// define custom rule with unique email
			this.$validator.extend('unique', {
				getMessage: (field, [args]) => 'This ' + field + ' has already exists',
				validate: (value, [args]) => {
					this.$http.post('/user/checkMailUpdate', { 'id': this.editUser.id, 'field': 'email', 'email': this.editUser.email, '_token': this.token}).then((response)=>{
						this.existedFlag =  response.data;
					});
					return this.existedFlag < args;				
				}
			});
			
			// define custom rule with unique name
			this.$validator.extend('nameunique', {
				getMessage: (field, [args]) => 'This ' + field + ' has already exists',
				validate: (value, [args]) => {
					this.$http.post('/user/checkUsernameUpdate', { 'id': this.editUser.id, 'field': 'username', 'username': this.editUser.username, '_token': this.token}).then((response)=>{
						 this.existedFlagName =  response.data;
					});
					return this.existedFlagName < args;				
				}
			});
			
			//this.loadImage();
		},
		methods: {
			loadImage: function() {
				this.$http.get('/' + this.path + this.editUser.avatar).then((response) => {
				}, (response) => {
					this.flagCheckAvatar = 1;
				});
			},
			// insert data on users table
			updateUser: function(id){
				this.editUser._token = this.token;
				this.editUser.permission = '[{admin:true}]';
				
				this.$validator.validateAll().then(result => {	    	
		    		if(result){
		    			this.$http.post('/user/change', this.editUser).then((response)=>{
		    				if(response.status === 200)
		    				{	
		    					this.showMessage(this.trans('user.title_danger'), this.trans('user.update_success'), 'success');
	    					}
		    				else{
	    						this.showMessage(this.trans('user.title_danger'), this.trans('user.update_error'), 'success');
	    					}
						}, (response)=>{
							this.showMessage(this.trans('user.title_danger'), response.status + ' ' +response.statusText, 'danger');
							return false;
						});
		    		}
				});
			},
			changeAvatar: function(e){
				var files = e.target.files || e.dataTransfer.files;
				if (files.length)
				{
					this.input = event.target;
				}
			},
	         cancleUpdate: function(){
	        	 location.href = '/user/index';
	         },
	         changeProfileImage: function(e){
				 this.$validator.validateAll().then(result => {
					 if(result)
					 {
						var files = e.target.files || e.dataTransfer.files;
						if (files.length)
						{
							this.input = event.target;
						}
						if (this.input.files && this.input.files[0]) {
							var reader = new FileReader();
							// create a new FileReader to read this image and convert to base64 format
							reader.onload = (e) => {
								var output = $('.profile-user-img');
								output.attr('src', e.target.result);
							};
							reader.readAsDataURL(this.input.files[0]);
							$('.profile-user-img').css('height','100');
						}
					 }
				 });
	         },
	         saveChangePassword: function(){
	        	 // save image profile
	        	 if (this.input.files && this.input.files[0]) {
		            // create a new FileReader to read this image and convert to base64 format
		            var reader = new FileReader();
		            
		            // Define a callback function to run, when FileReader finishes its job
		            reader.onload = (e) => {
		                // Read image as base64 and set to this.newUser.avatar
		            	var images = {
		            			'name': this.input.files[0].name,
		            			'data': e.target.result
		            	};
		            	var inputdata = {
	            			'id': this.editUser.id,
	            			'dataImage': images,
	            			'newPassword': this.newPassword,
	            			'_token': this.token
		            	};
		            	this.postUpdate(inputdata);
		            }
		           reader.readAsDataURL(this.input.files[0]);
	        	 }
	        	 else
	        	 {
	        		 var inputdata = {
            			'id': this.editUser.id,
            			'newPassword': this.newPassword,
            			'_token': this.token
	            	};
		            this.postUpdate(inputdata);
	        	 }
	        },
	        postUpdate: function(data){
	        	this.$http.post('/user/changeAvatar', data).then((response)=>{
            		if(response.status === 200)
            		{
						this.showMessage(this.trans('user.title_danger'), this.trans('user.update_success'), 'success');	
            		}else{
						this.showMessage(this.trans('user.title_danger'), this.trans('user.update_error'), 'danger');
					}
            	});
	        }
		}
	}

</script>
