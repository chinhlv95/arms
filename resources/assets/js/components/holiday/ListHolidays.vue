<template>
	<div class="box box-primary">
			<div class="box-header  with-border">
				<h3 class="box-title"> {{trans('holiday.page_title')}} </h3>
			</div>
			<div class="box-body">
				<div id="project_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
					<div class="row">
						<!--search-->
						<div class="col-lg-6">
						</div>
						<div class="col-lg-6 text-right">
							<div class="dataTables_length">
								<label>
									<button id="holiday-create" class="btn btn-info" @click="showCreateFrm">{{trans('holiday.label.btn_create')}}</button>
								</label>
							</div>
						</div>
						<!--search-->

					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<table id="data_list" class="table table-bordered table-striped" v-if="holidays.length">
							<thead>
								<tr>
									<th width="3%">{{trans('holiday.label.no')}}</th>
									<th width="10%">{{trans('holiday.label.date')}}</th>
									<th>{{trans('holiday.label.note')}}</th>
									<th width="7%">{{trans('holiday.label.action')}}</th>
								</tr>
							</thead>
							<tbody>
							<tr v-for="(item, key) in holidays">
								<td>{{ key + 1 }}</td>
								<td class="">{{ item.day }}</td>
								<td class="">{{ item.note }}</td>
								<td class="action">
									<a href="javascript:void(0)" class="edit-holiday" @click.prevent="showFormEdit(item.id,item.day,item.note)">{{trans('configmanager.label.label_edit')}}</a>
									<a href="#" class="delete-holiday" @click="showPopupDelete(item.id)">{{trans('configmanager.label.label_delete')}}</a>
								</td>
							</tr>
							</tbody>
							<tfoot>
								<tr>
									<th>{{trans('holiday.label.no')}}</th>
									<th>{{trans('holiday.label.date')}}</th>
									<th>{{trans('holiday.label.note')}}</th>
									<th>{{trans('holiday.label.action')}}</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>

			<!--modal frm-->
			<div id="myModal-frm" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">{{trans('holiday.label.label_dialog_title')}}</h4>
						</div>
						<div class="modal-body">
							<div :class="{'form-group':true, 'has-error': errors.has('holiday-name')}">
								<input type="hidden" name="id" v-model="currentHolidayId">
								<label for="holiday-date">{{trans('holiday.label.date_input')}}</label>
								<input  type="hidden" id="holiday-date" name="holiday-name" v-model="currentHoliday" :class="{'form-control': true, 'is-danger': errors.has('holiday-name')}">
								<date-picker-box v-if="flag" :defaultDate="currentHoliday" :language="local" @update-date="setDate" classArr="form-control"></date-picker-box>
								<span v-show="errors.has('holiday-name')" class=" help-block is-danger">
									{{ errors.all().indexOf('The holiday-name field is required.') > -1 ? trans('holiday.message.require') : trans('holiday.message.holiday_unique') }}
								</span>
							</div>
							<div class="form-group">
								<label for="holiday-note">{{trans('holiday.label.note_input')}}</label>
								<input type="text" id="holiday-note" class="form-control" v-model="currentHolidayNote">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('holiday.label.label_dialog_close')}}</button>
							<button type="button" class="btn btn-info" id="edit-btn" @click="editHoliday">{{trans('holiday.label.label_dialog_save')}}</button>
							<button type="button" class="btn btn-info" id="create-btn" @click="createHoliday">{{trans('holiday.label.label_dialog_save')}}</button>

						</div>
					</div>

				</div>
			</div>
			<!--modal frm-->

			<!--modal delete-->
			<div id="myModal-delete" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">{{trans('holiday.label.title_delete')}}</h4>
						</div>
						<div class="modal-body">
							<p> {{trans('holiday.message.confirm_delete')}}</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" @click="currentHolidayId = 0" data-dismiss="modal">{{trans('holiday.label.label_dialog_close')}}</button>
							<button type="button" class="btn btn-info" @click="deleteHoliday">{{trans('holiday.label.to_delete')}}</button>
						</div>
					</div>

				</div>
			</div>
			<!--end modal delete-->
	</div>
</template>
<script>
	export default {
		props: ['initdata','local'],
		data() {
			return {
				holidays:[],
				key: '',
				token : $('meta[name=csrf-token]').attr('content'),
				currentHolidayId: 0,
				currentHoliday: '',
				currentHolidayNote: '',
                message: '',
				flag: false
			}
		},
		mounted: function(){
			this.holidays = this.initdata;
			var _self = this;
			$(document).on('show.bs.modal','#myModal-frm',function (e) {
				if(e.target !== e.currentTarget) return;
				_self.$validator.clean();
				_self.flag = true;
			});
			$(document).on('hide.bs.modal','#myModal-frm',function (e) {
				if(e.target !== e.currentTarget) return;
				_self.flag 					= false;
				_self.currentHolidayId 		= 0;
				_self.currentHoliday 		= '';
				_self.currentHolidayNote 	= '';
			});

			//custom ruler
			this.$validator.extend('unique',{
				getMessage: (field) => this.trans('holiday.message.holiday_unique'),
				validate:(value)=> {
					return this.$http.get('/holiday/checkUnique?date=' + value +'&id=' +this.currentHolidayId ).then((response) => {
						return {
							valid: response.body.code,
						};
					}, (response) => {
							return false;
						});
					}
			});
			this.$validator.attach('holiday-name', 'required|unique');

		},
		methods: {
			showPopupDelete: function (holidayId) {
				this.currentHolidayId = holidayId;
				$('#myModal-delete').modal('show');
			},
			deleteHoliday: function () {
				var _self = this;
				var params = {'_token': this.token};
				this.$http.post('/holiday/delete/'+this.currentHolidayId, params).then( (response) => {
					if (response.body.code) {
					this.holidays = response.body.data;
					_self.currentHolidayId = 0;
					this.showMessage(this.trans('holiday.message.label_title_name'), this.trans('holiday.message.delete_success'), 'success');
				}else {
					this.showMessage(this.trans('holiday.message.label_title_name'), response.body.message, 'danger');
				}
				}, (response) => {
					this.showMessage(this.trans('holiday.message.label_title_name'), this.trans('holiday.message.delete_error'), 'danger');
				});
				$('#myModal-delete').modal('hide');
			},
			setDate: function (value) {
				this.currentHoliday = value;
			},
			showCreateFrm: function () {
				this.currentHolidayId = 0;
				$('#edit-btn').hide();
				$('#create-btn').show();
				$('#myModal-frm').modal('show');
			},
			showFormEdit: function (id,day,note) {
				this.currentHolidayId = id;
				this.currentHoliday = day;
				this.currentHolidayNote = note;
				$('#edit-btn').show();
				$('#create-btn').hide();
				$('#myModal-frm').modal('show');
			},
			editHoliday: function () {
				var _self = this;
				var params = {'date': _self.currentHoliday,'note': _self.currentHolidayNote,'_token': this.token};
				this.$validator.validate('holiday-name', _self.currentHoliday).then((result) => {
					if(result){
						this.$http.post('/holiday/edit/'+this.currentHolidayId, params).then( (response) => {
							if (response.body.code) {
								this.holidays = response.body.data;
								this.showMessage(this.trans('holiday.message.label_title_name'), this.trans('holiday.message.edit_success'), 'success');
							}else {
								this.showMessage(this.trans('holiday.message.label_title_name'), response.body.message, 'danger');
							}
						}, (response) => {
							this.showMessage(this.trans('holiday.message.label_title_name'), this.trans('holiday.message.edit_error'), 'danger');
						});
						_self.resetForm();
					}
				});
			},
			createHoliday: function () {
				var _self = this;
				var params = {'date': _self.currentHoliday,'note': _self.currentHolidayNote,'_token': this.token};
				this.$validator.validate('holiday-name', _self.currentHoliday).then((result) => {
					if(result){
						this.$http.post('/holiday/create', params).then( (response) => {
							if (response.body.code) {
								this.holidays = response.body.data;
								this.showMessage(this.trans('holiday.message.label_title_name'), this.trans('holiday.message.create_success'), 'success');
							}else {
								this.showMessage(this.trans('holiday.message.label_title_name'), response.body.message, 'danger');
							}
						}, (response) => {
								this.showMessage(this.trans('holiday.message.label_title_name'), this.trans('holiday.message.create_error'), 'danger');
						});
						_self.resetForm();
					}
				});
			},
			getListHoliday: function (key) {
				if (!!key) {
					key = '';
				}
			},
			resetForm: function () {
				this.currentHolidayId = 0;
				this.currentHoliday = '';
				this.currentHolidayNote = '';
				this.$validator.clean();
				$('#myModal-frm').modal('hide');
				this.flag = false;
			}
		}
	}
</script>