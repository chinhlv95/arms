<template>
    <div class="row">

        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <!-- /box-body -->
                <div class="box-body box-profile">
                    <div class="avatar_wrapper">
                        <i class="fa fa-pencil change_icon" aria-hidden="true"></i>
                        <input type="file" name="avatar" v-on:change="preview('.avatar')" class="avatar change_avatar" v-validate="'ext:jpeg,png,jpg,gif,svg,JPEG,PNG,JPG,GIF,SVG'">

                        <img onerror="/images/avatar_default.png" class="profile-user-img img-responsive img-circle" :src="user.avatar != null ? 'upload/avatar/' + user.avatar : '/images/avatar_default.png' " :alt=" trans('profile.label.avarta_placeholder') " />

                        <span v-show="errors.has('avatar')" class="help-block has-error is-danger text-center">{{ trans('profile.message.upload_avatar_error') }}</span>
                    </div>
                    <h3 class="profile-username text-center"> {{ user.fullname }} </h3>
                    
                    <ul class="list-group list-group-unbordered" v-if="user.id_resource != 0">
					    <!--change password-->
					    <li class="list-group-item">
					    	<label for="newPassword"><a class="text-center">{{trans('user.label.label_edit_password')}}</a></label>
					    	
					    	<div :class="{'form-group': true, 'has-error': errors.has('new_password')}">
			                  	<input type="password" v-model="userdata.new_password" class="form-control" v-validate="'min:8|max:16'" id="new_password" placeholder="Enter new password" name="new_password" >
			                  	<span v-show="errors.has('new_password')" class="help-block">{{ trans('user.messages.pass_length') }}</span>
			                </div>
					    </li>
				    </ul>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.Profile Image -->
            <!-- About Me Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('auth.profile_about_me')}}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b><i class="fa fa-user-circle-o margin-r-5" aria-hidden="true"></i>{{trans('auth.profile_fullname')}}</b> <a class="pull-right">{{userdata.fullname}}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fa fa-id-card margin-r-5"></i>{{trans('auth.profile_id')}}</b> <a class="pull-right">{{userdata.member_code}}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fa fa-envelope margin-r-5"></i>{{trans('auth.profile_email')}}</b> <a class="pull-right">{{userdata.email}}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fa fa-phone margin-r-5"></i>{{trans('auth.profile_phone')}}</b> <a class="pull-right">{{userdata.calling_code}} {{userdata.phone}}</a>
                        </li>
                        <li class="list-group-item">
                            <b><i class="fa fa-skype margin-r-5"></i>{{trans('auth.profile_skype')}}</b> <a class="pull-right">{{userdata.skype}}</a>
                        </li>
                    </ul>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>

        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{trans('auth.profile_edit')}}</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="fullname">{{trans('auth.profile_fullname')}}</label>
                            <input type="text" name="fullname" class="form-control" id="fullname" v-model="userdata.fullname" :placeholder="trans('profile.label.name_input_placeholder')">
                        </div>
                        <div :class="{'form-group': true, 'has-error': errors.has('email') || errors.has('unique') }">
                            <label for="email">{{trans('auth.profile_update_email_label')}}</label>
                            <input type="text" class="form-control" data-vv-delay="500" v-validate="'email|unique:1'" name="email" id="email" v-model="userdata.email"  :placeholder="trans('profile.label.email_input_placeholder')">

                            <span v-show="errors.has('email') || errors.has('unique')" class="help-block has-error">
								{{ errors.all().indexOf('The email field must be a valid email.') > -1 ? trans('profile.message.email_error_format') : trans('profile.message.email_already_existed') }}
							</span>
                        </div>
                        <div class="form-group">
                            <label for="phone">{{trans('auth.profile_update_phone_label')}}</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-control" style="width: 100%;" id="calling_code" v-model="userdata.calling_code" name="calling_code">
                                        <option v-for="(item, key) in callingcode" :selected="key === userdata.calling_code" :value="key">{{ item }}</option>
                                    </select>
                                </div>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                                        <input id="phone" type="text" v-validate="'numeric'" v-model="userdata.phone"  :placeholder="trans('profile.label.phone_input_placeholder')" name="phone" class="form-control" aria-invalid="false">
                                    </div>
                                    <span v-show="errors.has('phone')" class="help-block has-error">{{ trans('profile.message.phone_error_format') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="skype">{{ trans('profile.label.skype') }}</label>
                            <input type="text" class="form-control" id="skype" name="skype" :placeholder="trans('profile.label.skype_input_placeholder')" v-model="userdata.skype">
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="button" v-on:click="updateProfile(user.id)" class="btn btn-primary">{{trans('auth.profile_update_btn')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        props:['user', 'callingcode'],

        data(){
            return {
                userdata: {
                    fullname: this.user.fullname != 'null' ? this.user.fullname : '',
                    email: this.user.email != 'null' ? this.user.email : '',
                    phone: this.user.phone != 'null' ? this.user.phone : '',
                    skype: this.user.skype != 'null' ? this.user.skype : '',
                    member_code: this.user.member_code != 'null' ? this.user.member_code : '',
                    calling_code: this.user.calling_code != 'null' ? this.user.calling_code : '',
                    new_password: '',
                },
                token : $('meta[name=csrf-token]').attr('content'),
                existedFlag : '',
            }
        },
        created: function(){
            var _self = this;
            // define custom rule with unique name
			this.$validator.extend('unique', {
				getMessage: (field, [args]) => 'existed',
				validate: (value, [args]) => {
					this.$http.get('/profile/checkUnique?id=' + this.user.id + '&field=email&param=' + value).then((response)=>{
						 _self.existedFlag =  response.data;
					});
					return _self.existedFlag < args;				
				}
			});
        },
        methods: {
            //update profile
            updateProfile: function(id)
            {
                var _self = this,
                    avatar = $('.change_avatar')[0].files[0];
                    
                this.$validator.validateAll().then(result => {
                    if(result)
                    {
                        var formData = new FormData();
                        formData.append('fullname', _self.userdata.fullname);
                        formData.append('email', _self.userdata.email);
                        if(typeof avatar != 'undefined')
                        {
                            formData.append('avatar', avatar);
                        }
                        if(_self.userdata.new_password != '')
                        {
                            formData.append('password', _self.userdata.new_password)
                        }
                        formData.append('calling_code', _self.userdata.calling_code);
                        formData.append('phone', _self.userdata.phone);
                        formData.append('skype', _self.userdata.skype);
                        formData.append('id', id);
                        formData.append('_token', _self.token);

                        this.$http.post('/profile/update', formData).then( (response) => {
                            this.showMessage(this.trans('auth.profile_edit'), this.trans('auth.profile_update_success'), 'success');
                        }, (response) => {
                            this.showMessage(this.trans('auth.profile_edit'), this.trans('auth.profile_update_error'), 'success');
                            return false;
                        });
                    }
                });
            },
            //preview avatar file
            preview: function(avatar)
            {
                this.$validator.validateAll().then(result => {
                    if(result)
                    {
                        var fileReader = new FileReader();
                        fileReader.onload = function(e){
                            var output = $('.profile-user-img');
                            output.attr('src', fileReader.result);
                        }
                        fileReader.readAsDataURL($(avatar)[0].files[0]);
                    }
                });
            }
        }
    }
</script>