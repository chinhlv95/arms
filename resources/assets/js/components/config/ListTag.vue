<template>
	<div class="box box-primary">

			<div class="box-header  with-border">
				<h3 class="box-title"> {{trans('tags.page_title')}} </h3>
			</div>
			<div class="box-body">
				<div id="project_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
					<div class="row">
						<!--search-->
						<div class="col-lg-6">
							<div id="example1_filter" class="dataTables_length text-left">
								<label>{{trans('tags.label.label_search')}}
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-search"></i></div>
										<input type="text" class="form-control" id="input-search" data-mask="" data-vv-delay="1000" :placeholder="trans('tags.label.label_search') + '...'" v-on:keyup="getListTag(key)" v-model="key">
									</div>
								</label>
							</div>
						</div>
						<div class="col-lg-6 text-right">
							<div class="dataTables_length">
								<label>
									<button class="btn btn-info"  @click="showFormCreate">{{trans('tags.label.label_create')}}</button>
								</label>
							</div>
						</div>
						<!--search-->

					</div>
				</div>


				<div class="row">
					<div class="col-sm-12">
						<table id="data_list" class="table table-bordered table-striped" v-if="tags.length">
							<thead>
							<tr>
								<th width="3%">{{trans('tags.label.label_no')}}</th>
								<th>{{trans('tags.label.label_name')}}</th>
								<th width="8%">{{trans('tags.label.label_action')}}</th>
							</tr>
							</thead>
							<tbody>
							<tr v-for="(item, key) in tags">
								<td>{{ key + 1 }}</td>
								<td class="tag-name=text">{{ item.tag_name }}</td>
								<td class="action">
									<a href="javascript:void(0)" class="edit-tag" @click.prevent="showFormEdit(item.id,item.tag_name)">{{trans('tags.label.label_edit')}}</a>
									<a href="#" class="delete-tag" data-toggle="modal" data-target="#myModal-delete" @click="showPopupDelete(item.id)">{{trans('tags.label.label_delete')}}</a>
								</td>
							</tr>
							</tbody>
							<tfoot>
							<tr>
								<th>{{trans('tags.label.label_no')}}</th>
								<th>{{trans('tags.label.label_name')}}</th>
								<th>{{trans('tags.label.label_action')}}</th>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>

				<!--modal edit-->
				<div id="myModal-frm" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">{{trans('tags.label.label_dialog_title')}}</h4>
							</div>
							<div class="modal-body">
								<div :class="{'form-group':true, 'has-error': errors.has('tag-name') || errors.has('unique')}">
									<input type="hidden" id="tag-id" name="id" v-model="currentTagId">
									<label for="tag-name">{{trans('tags.label.label_dialog_tag_name')}}</label>
									<input  type="text" id="tag-name" name="tag-name" v-model="currentTagName" v-validate="'required|unique'" :class="{'form-control': true, 'is-danger': errors.has('tag-name')||errors.has('unique')}" placeholder="tag name...">
									<span v-show="errors.has('tag-name')" class=" help-block is-danger">{{ errors.all().indexOf('The tag-name field is required.') > -1 ? trans('tags.message.required_tag') : trans('tags.message.unique_tag') }}</span>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('tags.label.label_dialog_close')}}</button>
								<button type="button" class="btn btn-info" id="edit-btn" @click="editTag">{{trans('tags.label.label_dialog_save')}}</button>
								<button type="button" class="btn btn-info" id="create-btn" @click="createTag">{{trans('tags.label.label_dialog_save')}}</button>

							</div>
						</div>

					</div>
				</div>
				<!--end modal edit-->

				<!--modal delete-->
				<div id="myModal-delete" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">{{trans('tags.label.label_delete_item')}}</h4>
							</div>
							<div class="modal-body">
								<span>{{trans('tags.label.label_dialog_delete_title')}}</span>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('tags.label.label_dialog_close')}}</button>
								<button type="button" class="btn btn-info" @click="deleteTags">{{trans('tags.label.label_dialog_yes')}}</button>
							</div>
						</div>

					</div>
				</div>
				<!--end modal delete-->
			</div>
	</div>

</template>
<script>
	export default {
		props: ['initdata'],
		data() {
			return {
				tags:[],
				key: '',
				token : $('meta[name=csrf-token]').attr('content'),
				currentTagId: 0,
				currentTagName: '',
                message: '',
                existedFlag: ''
			}
		},
		mounted: function(){
			this.tags = this.initdata;

			//custom ruler
            this.$validator.extend('unique',{
                getMessage: (field) => this.trans('tags.message.unique_tag'),
                validate:(value)=> {
					return this.$http.get('/tags/checkUnique?name=' + value +'&id=' +this.currentTagId ).then((response) => {
                        return {
                            valid: response.body.code,
                        };
                    }, (response) => {
                        return false;
                    });
                }
            });
		},
		methods: {
			showPopupDelete: function (tagId) {
				this.currentTagId = tagId;
			},
			getListTag: function (){
				this.$http.get('/tags/search?key=' + this.key).then( (response) => {
					if(response.body.code > 0){
						this.tags = response.body.data;
					}else{
						this.tag = [];
					}
				}, (response) => {
					this.showMessage(this.trans('tags.message.label_title_name'), response.status + ' ' +response.statusText, 'danger');
					return false;
				});
			},
			showFormEdit: function (id,name) {
                this.$validator.clean();
			    $('#edit-btn').show();
			    $('#create-btn').hide();
				this.currentTagId = id;
				this.currentTagName = name;
				$('#myModal-frm').modal({
					show: 'false'
				});
			},
            showFormCreate: function() {
                this.$validator.clean();
                $('#edit-btn').hide();
                $('#create-btn').show();
                $('#myModal-frm').modal({
                    show: 'false'
                });
                this.currentTagId = 0;
                this.currentTagName = '';
                this.$validator.clean();
            },
			createTag: function () {
                var _self = this;
                this.$validator.validateAll().then((result) => {
                    if(result){
                        this.$http.post('/tags/create', {'tag_name': this.currentTagName,'_token': this.token}).then( (response) => {
                            this.tags.unshift(response.body);
                            this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.create_tag_success'), 'success');
                        }, (response) => {
                            this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.create_tag_error'), 'danger');
                        });
                        _self.resetForm();
                    }
                });
            },
			editTag : function () {
				var _self = this;

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$http.post('/tags/edit/' + this.currentTagId, { 'tag_name': $('#tag-name').val(), '_token': this.token }).then((response) => {
                            if (response.body != '{}') {
								//show message update success
                                this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.edit_tag_success'), 'success');

								//set new value by tag_id
								var index = _.findIndex(_self.tags, function(o){
									return o.id == response.body.id;
								});

								if(index > -1)
								{
									_self.tags[index].tag_name = response.body.tag_name;
								}

                            } else {
                                this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.edit_tag_error'), 'danger');
                            }
                        }, (response) => {
                            this.showMessage(this.trans('tags.message.label_title_name'), response.status + ' ' +response.statusText, 'danger');
                            return false;
                        });

                        _self.resetForm();
                    }
                });

			},
			deleteTags: function () {
				var _self = this;
					_self.$http.get('/tags/check_using_tag?id=' + this.currentTagId).then( (response) => {
						if(response.body.code > 0){
							$('#myModal-delete').modal('hide');
							this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.tag_in_work_time'), 'danger');
						}else{

							this.$http.post('/tags/delete/'+this.currentTagId, {'_token': this.token}).then( (response) => {
								if (response.body) {

									$('#myModal-delete').modal('hide');

									this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.delete_tag_success'), 'success');

									var index = _.findIndex(_self.tags, function(o){
										return o.id == _self.currentTagId;
									});
									
									if(index > -1)
									{
										_self.tags.splice(index, 1);
									}

								}else{
									this.showMessage(this.trans('tags.message.label_title_name'), this.trans('tags.message.delete_tag_error'), 'danger');
								}
							});
						}

				}, (response) => {
					this.showMessage(this.trans('tags.message.label_title_name'), response.status + ' ' +response.statusText, 'danger');
					return false;
				});
			},
			resetForm: function () {
				$('#tag-id').val("");
				$('#tag-name').val("");
                this.$validator.clean();
                $('#myModal-frm').modal('hide');
			},
		}
	}
</script>