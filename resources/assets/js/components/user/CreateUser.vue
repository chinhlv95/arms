<template>
	<!-- general form elements -->
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">{{trans('user.title_create')}}</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form role="form" method="POST" enctype="multipart/form-data" id="formCreateUser">
				<div class="box-body">
					<!--username-->
					<div :class="{'form-group': true, 'has-error': errors.has('username') || errors.has('nameunique')}">
						<label for="username">{{trans('user.label.label_username')}}</label>
						<div class="input-group">
							<span class="input-group-addon">@</span>
							<input type="text" class="form-control" data-vv-delay="500" v-validate="'required|nameunique:1'" placeholder="Username" name="username" v-model="newUser.username">
						</div>
						<span v-show="errors.has('username') || errors.has('nameunique')" class="help-block">
							{{ errors.all().indexOf('The username field is required.') > -1 ? trans('user.messages.user_required') : trans('user.messages.user_already_existed') }}
						</span>
					</div>
					
					<!--password-->
					<div :class="{'form-group': true, 'has-error': errors.has('password')}">
						<label for="password">{{trans('user.label.label_password')}}</label> 
						<input type="password" data-vv-delay="500" class="form-control" v-validate="'required|min:8|max:16'" id="password" placeholder="Password" name="password" v-model="newUser.password">
						<span v-show="errors.has('password')" class="help-block">{{ errors.all().indexOf('The password field is required.') > -1 ? trans('user.messages.pass_required') : trans('user.messages.pass_length') }}</span>
					</div>
					
					<!--fullname-->
					<div :class="{'form-group': true, 'has-error': errors.has('fullname')}">
						<label for="fullname">{{trans('user.label.label_fullname')}}</label> 
						<input type="text" data-vv-delay="500" class="form-control" id="fullname" placeholder="Full name" name="fullname" v-model="newUser.fullname">
						<span v-show="errors.has('fullname')" class="help-block">{{ errors.first('fullname') }}</span>
					</div>
					
					<!--email-->
					<div :class="{'form-group': true, 'has-error': errors.has('email') || errors.has('unique')}">
						<label>{{trans('user.label.label_email')}}</label> 
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
							<input type="email" data-vv-delay="500" v-validate="'email|unique:1'" class="form-control" placeholder="Email" name="email" v-model="newUser.email">
						</div>
						<span v-show="errors.has('email') || errors.has('unique')" class="help-block">{{ errors.all().indexOf('The email field must be a valid email.') > -1 ? trans('user.messages.email_invalid') : trans('user.messages.email_already_existed') }}</span>
					</div>
					<!--phone number-->
					<div :class="{'form-group': true, 'has-error': errors.has('phone')}">
						<label>{{trans('user.label.label_phone')}}</label>
						<div class="row">
							<div class="col-md-2">
								<select class="form-control select2" style="width: 100%;" v-model="newUser.calling_code">
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
									<input type="text" data-vv-delay="500" v-validate="'numeric'" class="form-control" placeholder="Phone number" data-inputmask='"mask": "(999) 999-9999"' data-mask name="phone" v-model="newUser.phone">
									
								</div>
								<span v-show="errors.has('phone')" class="help-block">{{ trans('user.messages.phone_format') }}</span>
							</div>
						</div>
					</div>
					
					<!--skype-->
					<div class="form-group">
						<label for="skype">{{trans('user.label.label_skype')}}</label>
						<input type="text" class="form-control" id="skype" name="skype" placeholder="Skype" v-model="newUser.skype">
					</div>
					
					<!--avatar-->
					<div :class="{'form-group': true, 'has-error': errors.has('avatar')}">
						<label for="avatar">{{trans('user.label.label_avatar')}}</label>
						<input type="file" id="avatar" name="avatar" v-on:change="checkImageChange" v-validate="'mimes:image/jpeg,image/gif,image/png,image/jpg|size:2048'">
						
						<span v-show="errors.has('avatar')" class="help-block">{{ trans('user.messages.upload_avatar_error') }}</span>
					</div>
					
					<!--preview image-->
					<div class="image-preview" v-if="profileImage.length > 0">
						<img class="preview" :src="profileImage">
					</div>
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<a href="/user/index">
						<button type="button" class="btn btn-default">{{trans('user.label.label_cancel')}}</button>
					</a>
					<button type="button" class="btn btn-primary" v-on:click="submitForm">{{trans('user.label.label_save')}}</button>
				</div>
			</form>
		</div>
	</div>
</template>
<script>
	export default {
		props: ['callingcode', 'resource'],
		data() {
			return {
				newUser : {'username':'', 'password':'', 'fullname':'', 'email':'', 'calling_code': '+84', 'phone':'', 'skype':'', 'avatar':''},
				token : $('meta[name=csrf-token]').attr('content'),
				files : {'name': '', 'data': ''},
				existedFlag : '',
				flagImage: false,
				input: {},
				profileImage: '',
				existedFlagUsername : '',
			}
		},
		created: function(){
			// clear form
			this.clearForm();
			// define custom rule with unique name
			this.$validator.extend('unique', {
				getMessage: (field, [args]) => this.trans('user.messages.email_already_existed'),
				validate: (value, [args]) => {
					this.$http.post('/user/checkMailExits', {'field': 'email', 'email' : value,'_token' : this.token}).then((response)=>{
						 this.existedFlag =  response.data;
					});
					return this.existedFlag < args;				
				}
			});
			
			this.$validator.extend('nameunique', {
				getMessage: (field, [args]) => this.trans('user.messages.user_already_existed'),
				validate: (value, [args]) => {
					this.$http.post('/user/checkUsername', {'field': 'username', 'username' : value,'_token' : this.token}).then((response)=>{
						 this.existedFlagUsername =  response.data;
					});
					return this.existedFlagUsername < args;				
				}
			});
		},
		methods: {
			// insert data on users table
			submitForm: function(){
				this.newUser._token = this.token;
				this.newUser.permission = '[{guest:true}]';
				this.$validator.validateAll().then(result => {	    	
		    		if(result) {
		    			if(this.flagImage){
		    				this.uploadImage();
						}
						else {
							this.saveInfo();
						}
		    		}
				});
			},
			// insert data
			saveInfo: function(){
				this.$http.post('/user/save', this.newUser).then((response)=>{
    				if(response.status === 200){
    					location.href = '/user/index'; 
					}else{
						this.showMessage(this.trans('user.title_danger'), this.trans('user.insert_error'), 'danger');
					}
				}, (response)=>{
					this.showMessage(this.trans('user.title_danger'), response.status + ' ' +response.statusText, 'danger');
					return false;
				});
			},
			// check avatar field input changed yes or no?
			checkImageChange(e) {
				var files = e.target.files || e.dataTransfer.files;
				if (files.length)
				{
					this.flagImage = true;
					this.input = event.target;
				}
				return this.flagImage;
		    },
			uploadImage() {
	            // Ensure that you have a file before attempting to read it
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
	                	this.newUser.avatar = images;
	                	this.saveInfo();
	                }
	               reader.readAsDataURL(this.input.files[0]);
	            }
				
	         },
			// reset form
			clearForm: function(){
				$('#formCreateUser').each(function(){
					this.reset();
				})
			}
			
		}
	}

</script>
