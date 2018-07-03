<template>
<div class="box box-primary">
        <!-- Loading (remove the following to stop the loading)-->
        <div class="overlay">
            <i class="fa fa-refresh fa-spin"></i>
        </div>

        <div class="box-header with-border">
            <!-- header control-->
            <div class="row form-group">
                <div class="col-md-8">
                    <div class="form-inline">
                        <div :class="{'form-group': true,'v-align-top': true , 'form_input_item': true, 'project_list': true}">
                            <select2 v-if="projects.length > 0"  :data="projects" @getValue = "setProjectId" :default_option="trans('worktime.label.label_option')" :current_value="project_id">aaa</select2>

                            <select2 v-else="projects.length <= 0"  :data="projects" @getValue = "setProjectId" :default_option="trans('worktime.label.label_option')" :current_value="project_id">aa</select2>
                            
                            <span v-if="errorMessage.length > 0" class="help-block">
                                <p v-show="errorMessage[0].select_project">{{ errorMessage[0].select_project }}</p>
                            </span>
                        </div>
                    
                        <div :class="{'form-group': true, 'v-align-top': true, 'form_input_item': true}" >
                            <input type="number" data-vv-delay="500"  step="0.25" min="0.25" id="entry_time" class="form-control" name="number" v-model="working_time">
                            <label for="entry_time">{{ trans('worktime.label.label_time') }}</label>
                            <span v-if="errorMessage.length > 0" class="help-block">
                                <p v-show="errorMessage[0].working_time">{{ errorMessage[0].working_time }}</p>
                            </span>
                        </div>

                        <div :class="{'form-group': true, 'form_input_item': true}">
                            <date-picker id="dateWorkTime" :language="local" @update-date="updateDate" class="datepicker"></date-picker>
                            <label for="dateWorkTime">{{ this.working_date  === formatDateTime(new Date()) ? this.trans('worktime.label.label_today')  : this.working_date }}</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-right">
                    <button type="button" class="btn btn-info" id="btnAddProjectUserSubmit" v-on:click="createWorkTime({project_id: project_id, entry_time: working_time, working_date: working_date, tags: tagsArray })">{{trans('worktime.label.label_button_submit')}}</button>
                </div>
            </div>

        <div class="row">
            <div class="col-md-6">
                <!-- tags component call -->
                <input-tag :placeholder="trans('worktime.label.label_tags') + '...'"  :tags="tagsArray" id="entry_tag"></input-tag>
            </div>
        </div>

        <!-- project indicator progress -->
        <div class="row col-md-12" style=" margin-top: 10px;">
            <label>{{ trans('worktime.label.label_projects_hours') }} <span v-for="(item, key) in items.data" v-if="key === 'Today'">{{item.total}}{{ trans('worktime.label.label_time') }} </span></label>
            <div class="row progress-bar" >
                <div class="partial-progress" v-for="item in dataProgressBar" :style="{'background-color' : '#' + items.color[item.project_id], width: (item.work_time * 100)/totalTimeToday +'%'}">
                    <span class="tooltiptext">
                        {{item.name}}
                        {{item.work_time}} hour(s) <br/>
                    </span>
                </div>
            </div>
        </div>
        <!-- /.project indicator progress -->

        <!-- tag indicator bar -->
        <div class="row col-md-12">
            <label>{{ trans('worktime.label.label_tags_indicator') }}</label>
                <div class="row progress-bar">
                    <div class="partial-progress tag_indicartor" v-for="item_tag in dataTagIndicator" :style="{'background-color' : item_tag.tag_color, width: (item_tag.tag_number * 100)/totalTagToday +'%'}">
                        <span class="tooltiptext">
                            {{item_tag.tag_name}}
                        </span>
                    </div>
                </div>
        </div>
        <!-- /.tag indicator bar -->

    </div>
    <!-- .header control-->

    <!-- box-body -->
    <div class="box-body no-padding ">
        <!--table data-->
        <div class="row lst-entries-time">
            <div class="tranparent-win" v-if="status" v-on:click="deadActiveDom()"></div>

                <div class="col-sm-12" v-for="(item, key) in items.data">

                    <table class="table table-condensed">
                        <tbody>
                            <tr>
                                <th width="10%"><h3>{{ key === 'Today' ? trans('worktime.label.label_today') : (key === 'Yesterday' ? trans('worktime.label.label_yesterday') : key  )}}</h3></th>
                                <th width="20%"></th>
                                <th width="55%"></th>
                                <th class="text-center" width="10%"><h3>{{item.total}} {{trans('worktime.label.label_time')}}</h3></th>
                                <th width="15%"></th>
                            </tr>
                        </tbody>
                    </table>

                    <table id="tbl_worktime_list" class="table table-condensed">
                        <thead>
                            <tr class="no-border">
                                <th width="5%"></th>
                                <th width="20%"></th>
                                <th width="60%"></th>
                                <th class="text-center" width="10%"></th>
                                <th width="15%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(project, _index) in item.projects">
                                <td class="text-center">
                                    <i class="fa fa-circle" aria-hidden="true" v-bind:style="{color: '#' + items.color[project.project_id]}"></i>
                                </td>

                                <td v-on:click="activeFormEdit(project.id, key, project.work_time)" class="edit_row">
                                    <select2 class="form-control input-process" v-if="status === project.id" :current_value="project.project_id" @on_change="updateProject(project.id, edit_project_id, key, _index)" @getValue="setEditProjectId" :data="projects"></select2>

                                    <div v-else>{{project.name}}</div>
                                </td>

                                <td v-on:click="activeFormEdit(project.id, key, project.work_time)">
                                    <input-tag :tags="project.tags[0].name" :id="'time-' + project.id" v-if="status == project.id" :on-change="updateTag" class="input-process form-control"></input-tag>
                                    <span v-else class="item-tag" v-for="tag_name in project.tags[0].name">{{tag_name}}</span>
                                </td>

                                <td  v-on:click="activeFormEdit(project.id, key, project.work_time)" class="text-center">
                                    <div v-if="status == project.id" :class="{'edit-wrapper': status == project.id}">
                                        <span v-if="editErrorMessage.length > 0" class="help-block">
                                            <p v-show="editErrorMessage[0].working_time">{{ editErrorMessage[0].working_time }}</p>
                                        </span>
                                        <input type="number" :id="'tag_name-' + project.id" step="0.25" min="0.25" class="form-control number input-process" data-vv-delay="50"  name="number" v-model="editWorkTime" v-on:change="updateWorkTime(project.id,editWorkTime)" />
                                    </div>
                                    <div v-else>{{project.work_time}}{{trans('worktime.label.label_time')}}</div>

                                </td>

                                <td class="text-center">
                                    <a href="#" data-toggle="modal" v-on:click.prevent="dataDelete = {'project_user_tag_id':project.id, 'key':key, 'index': _index, 'project_work_time': project.work_time, 'project_id': project.project_id, 'tag_deleted': project.tags[0].name}" data-target="#delete-item" >

                                        <span class="glyphicon glyphicon-remove hidden ico_delete_entry_time"></span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
               </table>
            </div>
        </div>
        <!-- .table data-->
    </div>

    <!-- load more -->
    <div class="row row-btn-loadmore">
        <div class="col-md-8 col-md-offset-5">
            <button type="button" class="btn btn-success btn-md" id="btn-load-more" v-on:click="loadMoreName"><i class="fa fa-refresh" aria-hidden="true" ></i>&nbsp{{trans('worktime.label.label_load_more')}}</button>
        </div>
    </div>
    <!--/.load more -->

    <!-- modal delete project -->
    <div id="delete-item" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans('worktime.label.label_massege_popup')}}</h4>
                </div>
                <div class="modal-body">
                    <p>{{trans('worktime.label.label_message_popup_delete')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('worktime.label.label_button_cancel')}}</button>
                    <button type="button" class="btn btn-primary"  v-on:click = "deleteWorkTime(dataDelete)">{{trans('worktime.label.label_button_yes')}}</button>
                </div>
            </div>
            <!--/.modal-content -->
        </div>
        <!--/.modal-dialog -->
    </div>
    <!-- /.modal delete project -->

</div>
</template>
<script>
    var curentProjectId;
    var curentDate;
	var _page = 0;
	import InputTag from 'vue-input-tag';
	export default{
		props: ['options', 'value', 'local', 'projects_data'],
		data(){
            return{
                items:'',
                projects:[],
                token : $('meta[name=csrf-token]').attr('content'),
                page: 0,
                tagsArray: [],
                project_id: '',
                edit_project_id: '',
                working_time: 0.25,
                working_date: this.local === 'vi' ? new Date().getDate() + '-' + (new Date().getMonth() + 1) + '-' + new Date().getFullYear() : new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + new Date().getDate(),
                errorMessage:[],
                editErrorMessage:[],
                dataProgressBar: [], //progress bar data
                totalTimeToday: 0,
                totalTagToday: 0,
                status : 0, // active form edit status
                countProjectLoaded: 0,
                dataTagIndicator: [],
                dataDelete: '',
                editWorkTime: 0,

            }
		},

		created: function(){
            this.getListWorkTime(this.page);
            this.getListProject();
		},

		methods: {

		    //$emit project id by select2 project component
			setProjectId(project_id)
            {
				this.project_id = project_id;
			},

            //$emit edit project id by select2 project component
            setEditProjectId(project_id)
            {
                this.edit_project_id = project_id;
            },
            
			//format datetime
			formatDateTime: function(d){
				return this.local === 'vi' ? d.getDate() + '-' + (d.getMonth() + 1) + '-' + d.getFullYear() : d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
			},

			//Update working_date when choose date picker
			updateDate: function(date){
				this.working_date = date;
			},

			//dead active Form Edit when click row tr
			deadActiveDom: function(){
				this.status = null;
			},

			//active Form Edit when click row tr
			activeFormEdit: function(id_project, currentTime, workTime){
                if (id_project != this.status) {
                    this.editErrorMessage = [];
                    curentDate = currentTime;
                    curentProjectId = id_project;
                    this.errorMessage = [];
                    this.status = id_project;
                    this.editWorkTime = workTime;
                }
			},

            setWorkTimeToVew: function (allData) {
                this.items = allData;
                var _data = this.items.data;
                var _countProjectLoaded = this.countProjectLoaded;
                var today = this.trans('worktime.label.label_today');
                this.setDataprogressBar(_data.Today);// huent: get data show progress bar
                this.setDataIndicatorTag(_data.Today);// theuNt : get data progress bar tag indicator
                //load more button handler
                $.each(this.items.data, function(index, value) {
                    _countProjectLoaded = _countProjectLoaded + value.projects.length;
                });

                if(_countProjectLoaded == this.items.total_project_work_time)
                {

                    $('#btn-load-more').prop('disabled', true);
                }
            },

			// theunt : list worktime
			getListWorkTime: function(page){
				this.$http.get('/worktime/getList/' + page).then((response)=>{
					if(response.bodyText != ""){
					    var allData = JSON.parse(response.bodyText);
                        this.setWorkTimeToVew(allData);
					}
					return false;

				}, (response) => {
					return false;
				});
			},

            //get project list on load
            getListProject: function(){
                this.projects = this.projects_data;
            },

			// huent: set data show progress bar
			setDataprogressBar: function(data){
				var tmpArr = [];
				this.totalTimeToday = 0;
				// get data worktime today
				if(data){
					for (var i = 0; i < data.projects.length; i++) {
						if(!tmpArr.hasOwnProperty(data.projects[i].project_id)) {
							tmpArr[data.projects[i].project_id] = {
								'project_id': data.projects[i].project_id,
								'work_time': 0,
								'name': data.projects[i].name
							};
						}
						tmpArr[data.projects[i].project_id].work_time = parseFloat(tmpArr[data.projects[i].project_id].work_time) + parseFloat(data.projects[i].work_time);
						this.totalTimeToday = parseFloat(this.totalTimeToday) + parseFloat(data.projects[i].work_time);
				    }
				}
				this.dataProgressBar = tmpArr.filter(function(item){
					return (item !== null || typeof(item) !== "undefined");
				});
			},
            // TheuNt : set data for indicator tag progress
            setDataIndicatorTag: function(data){
                var tag_data_tmp = [],
                    tag_data = [];
                if(data != null){
                    var projects = data.projects;
                    var l = projects.length;
                    for (var i = 0; i < l; i++){
                        var tag_name = data.projects[i].tags[0].name;
                        var tag_color = data.projects[i].tags[0].color;
                        if (tag_name.length > 0) {
                            for (var tag_index in tag_name){
                                tag_data_tmp.push({'tag_name': tag_name[tag_index], 'tag_color':'#'+tag_color[tag_index], 'tag_number': 1});
                            }
                        }
                    }
                }
                tag_data_tmp.forEach(function(e){
                    if (!this[e.tag_name]) {
                        this[e.tag_name] = { tag_name: e.tag_name, tag_color: e.tag_color, tag_number: 0 };
                        tag_data.push(this[e.tag_name]);
                    }
                    this[e.tag_name].tag_number += e.tag_number;
                }, Object.create(null));
                var total = 0;
                if (tag_data.length > 0) {
                    tag_data.forEach(function(e){
                        total = total + parseInt(e.tag_number);
                    });
                }
                this.totalTagToday = total;
                this.dataTagIndicator = tag_data;
            },

            // set data for indicator update tag progress
            updateTagIndicatorTag(data) {
                data = JSON.parse(data);
                this.totalTagToday = data.total;
                this.dataTagIndicator = data.tag_data;
            },

			// load mode
			loadMoreName: function(){
				$('.box-primary .overlay').css('display','block');
				var self = this;
				setTimeout(function(){
					$('.box-primary .overlay').css('display','none');
					self.page = self.page + 1;
					self.getListWorkTime(self.page);
				},2000);
			},

			//theunt : create new worktime
			createWorkTime: function(data){
                var _tags = _.uniqBy(data.tags,_.trim);
                data.tags = _tags;
                
                this.$http.post('/worktime/createWorkTime', {'data':data, _token: this.token}).then((response)=>{
                    if(response.bodyText.length > 0){
                        var _objReturn = JSON.parse(response.bodyText);
                        if (typeof _objReturn.data == 'undefined' || !_objReturn.data) {
                            if(_objReturn.errors.length != ''){
                                this.errorMessage = [];
                                this.errorMessage.push(_objReturn.errors);
                            }
                        } else {
                            //reset all data from
                            $('#project_select').val(null).trigger("change");
                            this.working_time = '0.25';
                            this.working_date = this.formatDateTime(new Date());
                            this.tagsArray = [];
                            this.errorMessage = []; //reset errorMessage array

                            //reload data list
                            this.getListWorkTime(this.page);
                        }
                    }
                }, (response)=>{
                    return false;
                });

			},

			//update project user
			updateProject: function(id, project, day, index){
				var _data = this.items.data;

				this.$http.get('/worktime/updateProject?id=' + id + '&project_id=' + project).then((response)=>{
					if(response.body != null){
					    //set updated name & project_id
						this.items.data[day].projects[index].name  = response.body;
                        this.items.data[day].projects[index].project_id  = parseInt(project);

						//if day === today, then update progress indicator
						if(day === 'Today'){
							this.setDataprogressBar(_data.Today);
						}
					}
				}, (response)=>{
					return false;
				});
			},

			//update time
			updateWorkTime: function(id, work_time){
                this.$http.post('/worktime/updateWorkTime', {'id': id, 'work_time': work_time, _token: this.token, page: this.page}).then((response)=>{
                    if(response.body.code){
                        console.log(response.body.data);
                        this.setWorkTimeToVew(response.body.data);
                        this.editErrorMessage = [];
                    }else{
                        this.editErrorMessage.push({working_time: response.body.data});
                    }
                }, (response) => {
                    return false;
                });
			},


			//update tag project user
            updateTag:  function(tags) {
                var _tags = _.uniqBy(tags,_.trim);
                
                this.$http.post('/worktime/updateTag',{'project_user_id':curentProjectId, 'editTag': _tags,  _token: this.token}).then((response)=>{
                    if(response.bodyText.length > 0 && curentDate == "Today"){
                        this.updateTagIndicatorTag(response.bodyText);
                    }
                }).catch(function (error) {
                    return false;
                });

            },

			//delete Work Time
			deleteWorkTime: function(data){
                var self = this;
				var items = this.items, //project data object
                _dataPgoressBar = this.dataProgressBar, //indicator data rendering,
                    _dataTagIndicator = this.dataTagIndicator;
				this.$http.post('/worktime/deleteWorkTime',{'id':data.project_user_tag_id, _token: this.token}).then((response)=>{
					if(response.body.code){ //if server delete status is true
                        $('#delete-item').modal("hide");
						if(data.key === 'Today'){ // if key is Today
							//check Time same project more than 1
							var hasDuplicatedTime = items.data[data.key].projects.filter(function(item){
								return item.project_id === data.project_id;
							});

							//set data tag indicator again
                            _.remove(_dataTagIndicator, function(n) {
                                return data.tag_deleted.indexOf(n.tag_name) > -1;
                            });
                            this.dataTagIndicator = response.body.result.tag_data;
                            this.totalTagToday = response.body.result.total;
							//if same project work time more than 1 set work time indicator again
							if(hasDuplicatedTime.length > 1){
								_dataPgoressBar.map(function(e,i) {
									// set work time again
									if (e.project_id === data.project_id) {
										e.work_time = e.work_time - data.project_work_time;
									}
                                    self.totalTimeToday = parseFloat(items.data[data.key].total) - parseFloat(data.project_work_time);
								});

							}else{ //else this project id only work time then remove this
								_dataPgoressBar.map(function(e,i) {
									//if deleted project id equal project id of indicator data
									// => remove object
									if (e.project_id === data.project_id) {
										_dataPgoressBar.splice(i, 1);
									}
                                    self.totalTimeToday = parseFloat(items.data[data.key].total) - parseFloat(data.project_work_time);
								});
							}
						}

						//set data list again
						items.data[data.key].projects.splice(data.index, 1);

						items.data[data.key].total = parseFloat(items.data[data.key].total) - parseFloat(data.project_work_time);
					}else{
						return false;
					}
				},(response)=>{
					return false;
				});
			}
		},

		//inject components
		components: {
		    InputTag
		},
	}

</script>
