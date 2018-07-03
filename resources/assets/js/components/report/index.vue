<template>
	<div class="box box-primary">
	    <!-- Loading (remove the following to stop the loading)-->
	    <div class="overlay">
	      	<i class="fa fa-refresh fa-spin"></i>
	    </div>
	    <!-- end loading -->
		<div class="box-body">
			<div id="example1_wrapper content-report-project" class="dataTables_wrapper form-inline dt-bootstrap">
				<!-- option filter-->
				<div class="option-filter-project">
					<div class="row title-report">
						<div class="col-md-6">
							<h3>{{ trans('report.label.label_report') }}</h3>
						</div>
						<div class="col-md-6 text-right">
							<h4>
							<span v-if="indexClick === 0 && typeReport === 1">{{ trans('report.label.label_this_month') }}</span>
							<span v-else-if="indexClick === 0 && typeReport === 0">{{ trans('report.label.label_this_week') }}</span>
							<span v-else-if="indexClick === 1 && typeReport === 1">{{ trans('report.label.label_next_month') }}</span>
							<span v-else-if="indexClick === 1 && typeReport === 0">{{ trans('report.label.label_next_week') }}</span>
							<span v-else-if="indexClick === -1 && typeReport === 1">{{ trans('report.label.label_last_month') }}</span>
							<span v-else-if="indexClick === -1 && typeReport === 0">{{ trans('report.label.label_last_week') }}</span>
							<span v-else>{{setDaterange}}</span> 
								<i class="fa fa-angle-left" aria-hidden="true" v-on:click="increaseWeek"></i>
								<i class="fa fa-angle-right" aria-hidden="true" v-on:click="decreaseWeek"></i>
							</h4>
							<span v-if="typeReport == 1" class="type-report"><a v-on:click="setReportWeekly">{{ trans('report.link.link_weekly') }}</a> | <a style="color: #aaa" v-on:click="setReportMonthly">{{ trans('report.link.link_monthly') }}</a></span>
							<span v-else class="type-report"><a style="color: #aaa" v-on:click="setReportWeekly">{{ trans('report.link.link_weekly') }}</a> | <a v-on:click="setReportMonthly">{{ trans('report.link.link_monthly') }}</a></span>
						</div>
					</div>
					<div class="row display-filter">
						<div class="col-md-12">
							<fieldset class="collapsible">
								<legend v-on:click="showOptionFilter"><label><i :class="{'fa': true, 'fa-caret-right': flagShowFilter, 'fa-caret-down': flagHideFilter}" aria-hidden="true"></i> {{ trans('report.link.link_add_filter') }}</label></legend>
								
								<div class="row option-filter">
									<div class="col-md-8">
										<div class="row">
											<!--selectbox project-->
											<div class="col-md-4">
												<select-checkbox v-if="projects.length > 0" :selected="condition.project" :data="projects" id="select-box-projects" keyName="project" :placeholder="trans('report.placehoder.selectbox_project')" @add-condition="addConditionProject"></select-checkbox>

												<select-checkbox v-if="projects.length <= 0" :selected="condition.project" :data="projects" id="select-box-projects" keyName="project" :placeholder="trans('report.placehoder.selectbox_project')" @add-condition="addConditionProject"></select-checkbox>
												
											</div>
											
											<!--selectbox client-->						
											<div class="col-md-4">
												<select-checkbox  v-if="clients.length > 0" :selected="condition.client" :data="clients" keyName="client" id="select-box-client" :placeholder="trans('report.placehoder.selectbox_client')" @add-condition="addConditionClient"></select-checkbox>

												<select-checkbox  v-else="clients.length <= 0" :selected="condition.client" :data="clients" keyName="client" id="select-box-client" :placeholder="trans('report.placehoder.selectbox_client')" @add-condition="addConditionClient"></select-checkbox>
												
											</div>
											
											<!--selectbox tag-->
											<div class="col-md-4">
												<select-checkbox v-if="tags.length > 0" :selected="condition.tag" :data="tags" keyName="tag" id="select-box-tag" :placeholder="trans('report.placehoder.selectbox_tag')" @add-condition="addConditionTag"></select-checkbox>

												<select-checkbox v-if="tags.length <= 0" :selected="condition.tag" :data="tags" keyName="tag" id="select-box-tag" :placeholder="trans('report.placehoder.selectbox_tag')" @add-condition="addConditionTag"></select-checkbox>
											</div>
										</div>
									</div>
									
									<div class="col-md-4 text-right">
										<button type="button" class="btn btn-success" v-on:click="showData">{{ trans('report.button.button_apply') }}</button>
									</div>
										<!--/.col-3-->
								</div> <!-- /end row -->
							</fieldset>
						</div>
					</div>
					
				</div>
				<!-- / .option filter-->
				<!-- bar chart-->
				<div class="row">
					<div class="col-md-6 text-left">
						<label>{{ trans('report.label.label_total') }}: {{ Math.round(parseFloat(totalHourBarchar) * 1000)/1000 }} h</label>  
					</div>
					<div class="col-md-6 text-right">
						<label>{{ trans('report.label.label_export') }}: <a v-on:click="exportExcel" id="btn-export-excel">{{ trans('report.link.link_excel') }}</a></label>  
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div id="content-barchart" v-if="labelBarchart.length > 0">
							<bar-chart :datasets="datasetBarchart" :labels="arrLabelBarchart" label="Worktime"></bar-chart>
						</div>
						
					</div>
				</div>
				<!--end bar chart -->
				<div class="row lst-data-projects">
					<div class="col-md-8" id="dvData">
						<!--end row -->
						<table class="table table-condensed" id="data-list-project">
							<thead v-if="items.length > 0">
								<tr>
							      	<th style="width: 10px">#</th>
							      	<th>{{ trans('report.label.label_tabel_name_project') }}</th>
							      	<th>{{ trans('report.label.label_tabel_total_member') }}</th>
							      	<th>{{ trans('report.label.label_tabel_total_hour') }}</th>
							      	<th>{{ trans('report.label.label_tabel_standard') }}</th>
							    </tr>
							</thead>
						    <tbody>
							    <tr v-for="item in items">
							      	<td><i class="fa fa-circle" aria-hidden="true" v-bind:style="{ color: item.color}"></i></td>
							      	<td v-if="item.key"><a :href="mtoolresource[item.id_mtool_resource] + item.key" target="_blank">{{ item.name }}</a></td>
							      	<td v-else> {{ item.name }} </td>
							      	<td>
								      	<p v-if="item.total_member">{{ item.total_member }}</p>
							      		<p v-else>0</p>
							      	</td>
							      	<td>
							      		<p v-if="item.total_hour > 0">{{ Math.round(parseFloat(item.total_hour) * 1000)/1000 }}</p>
							      		<p v-else>0</p>
							      	</td>
							      	<td>{{ item.standard_time }}</td>
							    </tr>
							 </tbody>	
					  	</table>
					</div>
					<div class="col-md-1"></div>
					<!--chart-->
					<div class="col-md-3 text-center" v-if="dataChart.length">
						<ul id="legent-detail"></ul>
						<pie-chart :data="dataChart" :backgroundColor="backgroundColorChart" legentArea="legent-detail" :label="labelChart" @update-total-worktime="resetTotalWorktime"></pie-chart>
			            <label>{{ trans('report.label.label_total') }}:{{ Math.round(parseFloat(total_hour) * 1000)/1000 }} (h)</label>
					</div>
					<!--end pie chart -->
				</div>
				<!--end row-->
			</div>
		</div>
	</div>
</template>

<script>
	import moment from 'moment';
	export default {
		props: {
			local: {
				type: String,
				default: function(){
					return 'vi';
				}
			},
			user: '',
			mtoolresource: ''
		}, 
		data() {
			return {
				items: '', // contain list projects show in table
				tags: [], // get all tags => add name tags in checkbox
				clients: [], // get all clients => add name clients in checkbox
				projects: [], // get all projects => add name projects in checkbox
			    dataChart: [],// piechart
			    backgroundColorChart: [],// piechart
			    labelChart: [],// piechart
			    labelBarchart: [], // barchart
			    datasetBarchart: [], // barchart
			    arrLabelBarchart: [],// contain day of week
			    arrDayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thurday', 'Friday', 'Saturday', 'Sunday'],
			    total_hour: 0,
			    totalHourBarchar: 0,
			    condition: {'project': [], 'client': [], 'tag': [], 'daterange': [], 'member': []},
			    indexClick: 0, // number of click decrease/increase month or week
			    setDaterange: 'This week', // label display in change week/month
			    flagApply: 0, // check status click button Apply
			    flagChangeProject: 0,
			    flagChangeClient: 0, 
			    flagShowFilter: true, // 0: close
			    flagHideFilter: false,
			    dayOfWeek: [],
			    typeReport: 1 // 1: default/monthly
			}
		},
		created: function(){
			this.arrDayOfWeek = this.trans('report.week_day');
			this.getMember();
			// get tag
			this.getTag();
			// get client
			this.getClient();
			// get project
			this.getProject();
			// get data report
			this.changeWeek(this.indexClick);
			this.getData();
		},
		methods: {
			setReportWeekly: function(){
				this.typeReport = 0;
				this.indexClick = 0;
				this.changeWeek(this.indexClick);
				this.getData();
			},
			setReportMonthly: function(){
				this.typeReport = 1;
				this.indexClick = 0;
				this.changeWeek(this.indexClick);
				this.getData();
			},
			showOptionFilter: function(){
				if(this.flagShowFilter){
					$('.option-filter').slideDown();
					this.flagShowFilter = false;
					this.flagHideFilter = true;
				}
				else{
					$('.option-filter').slideUp();
					this.flagShowFilter = true;
					this.flagHideFilter = false;
				}
			},
			getMember: function(){
				this.$http.get('/user/getMember/' + this.user.id).then((response) => {
					if(response.status === 200){
						if(response.data){
							for(var i = 0; i < response.data.length; i++){
								this.condition.member.push(parseInt(response.data[i].id));
							}
						}
					}
				}, (response) => {
					return false;
				});
			},
			// add condition
			addConditionTag: function(data){
				this.condition.tag = this.addCondition(this.condition.tag, parseInt(data));
			},
			addConditionClient: function(data){
				this.flagChangeClient = 1;
				this.condition.client = this.addCondition(this.condition.client, parseInt(data));
				if(this.condition.client.length > 0){
					this.synchCondition(this.condition.client, 1);
				}
				else{
					this.condition.project = [];
					this.condition.tag = [];
				}
			},
			addConditionProject: function(data){
				this.flagChangeProject = 1;
				this.condition.project = this.addCondition(this.condition.project, parseInt(data));
				if(this.condition.project.length > 0){
					this.synchCondition(this.condition.project, 0);
				}
				else{
					this.condition.client = [];
					this.condition.tag = [];
				}
			},
			synchCondition: function(data, flag){// project changed =>flag = 0; client changed => flag = 1; tag changed => flag = 2
				this.$http.get('/project/synchCondition?data=' + data 
						+ '&flag=' + flag 
						+ '&dateFrom=' + this.condition.daterange['dateFrom'] 
						+ '&dateTo=' + this.condition.daterange['dateTo'])
						.then((response)=>{
					if(response.status === 200){
						if(response.data.project){
							for(var i = 0; i< response.data.project.length; i++){
								response.data.project[i] = parseInt(response.data.project[i]);
							};
						}
						
						if(response.data.client){
							for(var i = 0; i< response.data.client.length; i++){
								if(typeof(response.data.client[i]) === 'object'){ // value is null will be removed
									response.data.client.splice(i, 1);
								}
								else{
									response.data.client[i] = parseInt(response.data.client[i]);
								}
							};
						}
						this.condition.project = response.data.project;
						this.condition.client = response.data.client;
						
						this.flagChangeClient = 0;
						this.flagChangeProject = 0;
					}
				}, (response)=>{
					return false;
				});
			},
			// addCondition: if exits -> remove/splice this item, else -> push one item
			// addSelected: if exits -> break for loop, else -> push one item
			addCondition: function(array, value){
				var check = _.find(array, function(o){
					return o == value;
				});
				
				if(typeof check === 'undefined'){
					array.push(parseInt(value));
				}else{
					array = _.remove(array, function(n){
						return n != value;
					});
				}
				return array;
			},
			decreaseWeek: function(){
				this.indexClick = this.indexClick + 1;
				this.changeWeek(this.indexClick);
				this.getData();
			},
			increaseWeek: function(){
				this.indexClick = this.indexClick - 1;
				this.changeWeek(this.indexClick);
				this.getData();
			},
			// event when click button change week
			changeWeek: function(index){// index: number of click
				this.labelBarchart = [];
				this.arrLabelBarchart = [];
				if(this.typeReport == 1)// report monthly - default
				{
					var date = new Date(), y = date.getFullYear(), m = date.getMonth();
					var first = new Date(y, m + index, 1);
					var last = new Date(y, m + index + 1, 0);
					
					var from = this.local === 'vi' ? moment(first).format("DD-MM-YYYY") : moment(first).format("YYYY-MM-DD");
					var to = this.local === 'vi' ? moment(last).format("DD-MM-YYYY") : moment(last).format("YYYY-MM-DD");
					// set label chart. label chart is list day of week
					var numberDayOfWeek = moment(from, "YYYY-MM").daysInMonth();
					// set labelBarChart. labelBarChart is array of list label from firt day of month to last day of this month
					for(var i = 0; i < parseInt(numberDayOfWeek); i++){
						var new_date = this.local === 'vi' ? moment(from, "DD-MM-YYYY").add('days', i) : moment(from, "YYYY-MM-DD").add('days', i);
						var new_date_format = this.local === 'vi' ? moment(new_date).format("DD-MM-YYYY") : moment(new_date).format("YYYY-MM-DD");
						this.labelBarchart.push(new_date_format)
					}
					this.arrLabelBarchart = this.labelBarchart;
				}
				else{// report weekly
					var first = moment().weekday(index * 7 + 1)._d;
					var last = moment().weekday(index * 7 + 7)._d;
					for(var i = 0; i < 7; i++){
						this.arrLabelBarchart[i] = [];
						var day = moment().weekday(this.indexClick * 7 + 1 + i)._d;
						var dayFormat = this.local === 'vi' ? moment(day).format("DD-MM-YYYY") : moment(day).format("YYYY-MM-DD")
						this.labelBarchart.push(dayFormat);
						this.arrLabelBarchart[i].push(this.arrDayOfWeek[i]);
						this.arrLabelBarchart[i].push(dayFormat);
					}
				}
				
				// add date to array condition
				this.condition.daterange['dateFrom'] = this.local === 'vi' ? moment(first).format("DD-MM-YYYY") : moment(first).format("YYYY-MM-DD");
				this.condition.daterange['dateTo'] =  this.local === 'vi' ? moment(last).format("DD-MM-YYYY") : moment(last).format("YYYY-MM-DD");
				this.setDaterange = this.local === 'vi' ? moment(first).format("DD-MM-YYYY") : moment(first).format("YYYY-MM-DD");
			},
			// event when click button Apply
			showData: function(){
				this.getData();
			},
			getData: function(){
				this.dataChart = [];
				this.backgroundColorChart = [];
				this.labelChart = [];
				this.items = '';
				
				if(this.condition.project.length == 0 && this.condition.client.length == 0 && this.condition.tag.length == 0 ){
					this.flagApply = 0; // button Apply was clicked
				}
				else{
					this.flagApply = 1;
				}
				
				var _self_dataChart = this.dataChart;
				var _self_backgroundColorChart = this.backgroundColorChart;
				var _self_labelChart = this.labelChart;
				var self = this;
				this.total_hour = 0;
				this.totalHourBarchar = 0;
				this.$http.get('/project/filter?project=' + this.condition.project 
								+ '&client=' + this.condition.client 
								+ '&tag=' + this.condition.tag 
								+ '&dateFrom=' + this.condition.daterange['dateFrom'] 
								+ '&dateTo=' + this.condition.daterange['dateTo']
								+ '&flag='+ this.flagApply
								+ '&auth=' + this.user.id )
				.then((response)=>{
					if(response.data){
						this.items = response.data.data ? response.data.data : []; // items is list project selected
						
						// show data in table and in piechart
						if(this.items.length > 0){
							for(var i = 0; i< this.items.length; i++)
						    {
								if(this.items[i].total_hour)
								{
									this.total_hour = this.total_hour + parseFloat(this.items[i].total_hour);
									this.totalHourBarchar = this.totalHourBarchar + parseFloat(this.items[i].total_hour)
								}
								
								_self_dataChart.push(this.items[i].total_hour != null ? this.items[i].total_hour :0);
								_self_backgroundColorChart.push(this.items[i].color);
								_self_labelChart.push(this.items[i].name);
						    }
						}
						// set data show in barchart
						this.datasetBarchart = [];
						var arrayDataChart = [];
						if(response.data){
							for(var i = 0; i < response.data.chart.length; i++){
								var indexDay;
								var day = this.local === 'vi' ? moment(response.data.chart[i].working_date).format("DD-MM-YYYY") : moment(response.data.chart[i].working_date).format("YYYY-MM-DD");
								indexDay = this.labelBarchart.indexOf(day);
								arrayDataChart[indexDay] = Math.round(parseFloat(response.data.chart[i].total_hour) * 1000)/1000; 
							}
						}
						for(var j = 0; j < this.labelBarchart.length; j++){
							if(typeof(arrayDataChart[j]) === 'undefined'){
								this.datasetBarchart[j] = 0;	
							}
							else{
								this.datasetBarchart[j] = arrayDataChart[j];
							}
						}
						this.lstProject = this.items
					}
				}, (response)=>{// callback error
					return false;
				});
			},
			// getClient
			getClient: function(){
				this.$http.get('/project/getClient?auth=' + this.user.id).then((response)=>{
					if(response.status === 200)
					{
						if(response.data){
							for(var i = 0; i <response.data.length; i++)
							{
								this.clients.push({'key': response.data[i].id, 'value': response.data[i].name});
							}
						}
					}
					else
						return false;
				},(response)=>{
					return false;
				});
			},
			// getTag
			getTag: function(){
				this.$http.get('/project/getAllTag').then((response)=>{
					if(response.status === 200)
					{
						if(response.data){
							for(var i = 0; i <response.data.length; i++)
							{
								this.tags.push({'key': response.data[i].id, 'value': response.data[i].tag_name});
							}
						}
					}
					else
						return false;
				},(response)=>{
					return false;
				});
			},
			// get project
			getProject: function(){
				this.$http.get('/project/getAll?auth=' + this.user.id).then((response)=>{
					if(response.status === 200)
					{
						if(response.data){
							for(var i = 0; i <response.data.length; i++)
							{
								this.projects.push({'key': response.data[i].id, 'value': response.data[i].name});
							}
						}
					}
					else
						return false;
				},(response)=>{
					return false;
				});
			},
			// get tags selected
			getTagSelected: function(data){ // data is list of project selected
				this.$http.get('/project/tagSelected?project=' + data).then((response)=>{
					if(response.data){
						for(var i = 0; i < response.data.length; i++){
							this.addSelected(this.condition.tag, response.data[i].id)
						}
					}
				},(response)=>{
					return false;
				});
			},
			addSelected: function(array, value){
				if(array.length == 0){
					array.push(value);
				}
				else{
					for(var i = 0; i < array.length; i++){
						if(array[i] == value){
							break;
						}
						else{
							array.push(value);
							break;
						}
					}
				}
			},
			resetTotalWorktime: function(data){
				// reset total_hour
				this.total_hour = 0;
				for(var i = 0; i< data.length; i++)
				{
					this.total_hour = this.total_hour + parseFloat(data[i]);
				}
			},
			exportExcel: function(){
				var href = '/project/exportReport?project=' + this.condition.project
						+ '&client=' + this.condition.client
						+ '&tag=' + this.condition.tag
						+ '&dateFrom=' + this.condition.daterange['dateFrom']
						+ '&dateTo=' + this.condition.daterange['dateTo']
						+ '&flag='+ this.flagApply
						+ '&auth=' + this.user.id
						+ '&type=xlsx';
				var a = document.createElement("a");
				a.href = href;
				document.body.appendChild(a);
				a.click();
				a.remove();
			}
		}
	}
</script>