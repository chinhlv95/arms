
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./libs/bootstrap-datepicker.min');
require('./libs/bootstrap-datetimepicker.ja');
require('./libs/bootstrap-datetimepicker.vi');

require('./libs/select2.full.min');
require('./libs/jquery.nestable.js');

window.Vue = require('vue');
import vi from 'vee-validate/dist/locale/vi';
import VeeValidate, { Validator } from 'vee-validate';
import VueResource from 'vue-resource';
import VueRouter from 'vue-router';
import VueChart from 'vue-chartjs';
import PieChart from './components/custom/PieChart';
import DatePicker from './components/custom/DatePicker';
import DateBoxPicker from './components/custom/DateTimePickerBox';
import Select2 from'./components/custom/Select2';

import Pagination from'./components/custom/Pagination.vue';
import SelectCheckbox from './components/custom/SelectCheckbox';
import BarChart from './components/custom/BarChart';
import Nestable from './components/custom/NestableView';
import lodash from 'lodash';


//Add locale helper.
Validator.addLocale(vi);

Vue.use(VeeValidate, {
	  events: 'input|blur'
	});

Vue.use(VueResource);
Vue.use(VueRouter);
Vue.use(VueChart);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//third party plugin component
Vue.component('pie-chart', PieChart);
Vue.component('date-picker', DatePicker);
Vue.component('date-picker-box', DateBoxPicker);
Vue.component('select2', Select2);


Vue.component('pagination', Pagination);
Vue.component('select-checkbox', SelectCheckbox);
Vue.component('bar-chart', BarChart);

Vue.component('nesteable-tree', Nestable);

//common function

//-- transation
Vue.prototype.trans = (key) => {
    return _.get(window.trans, key, key);
};

//-- show message
Vue.prototype.showMessage = (MsgTitle, message, level) => {
	var html = '<div class="row alert-message"><div class="col-md-12">' +
                        '<div class="box-body">' +
                            '<div class="alert alert-'+ level +' alert-dismissible">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                                '<h4 id="title-message">'+ MsgTitle +'</h4>' +
                                '<p id="content-message">'+message+'</p>' +
                            '</div>' +
                        '</div>' +
                        '</div></div>';
		$(html).prependTo('.content.container-fluid').delay(3000).slideUp(300);
}

//-- trigger redirect back login when token expired
window.onload = function () {
	(function() {
		var origOpen = XMLHttpRequest.prototype.open;
		XMLHttpRequest.prototype.open = function() {
			this.addEventListener('load', function() {
				if (this.status == 401) {
					location.reload();
				}
			});
			origOpen.apply(this, arguments);
		};
	})();

	if (!Array.prototype.forEach) {
		Array.prototype.forEach = function(fn, scope) {
			for(var i = 0, len = this.length; i < len; ++i) {
				fn.call(scope, this[i], i, this);
			}
		}
	}
};

const CreateUser = Vue.component('create-user', require('./components/user/CreateUser.vue'));
const ListUser = Vue.component('list-user', require('./components/user/ListUser.vue'));
const UpdateUser = Vue.component('update-user', require('./components/user/UpdateUser.vue'));
const MainReport = Vue.component('main-report', require('./components/report/index.vue'));
const ListWorkTime = Vue.component('list-worktime',require('./components/worktime/ListWorkTime.vue'));
const UserProfile = Vue.component('user-profile',require('./components/user/Profile.vue'));

//Project Manager
const ProjectManagerList = Vue.component('project-manager-list',require('./components/projects/index.vue'));
const ProjectManagerCreate = Vue.component('project-manager-create',require('./components/projects/create.vue'));
const ProjectManagerUpdate = Vue.component('project-manager-update',require('./components/projects/update.vue'));

//Tag Manager
const TagsMaster = Vue.component('tags-list',require('./components/config/ListTag.vue'));


//division Manager
const DivisionManager = Vue.component('division-list',require('./components/division/division.vue'));
const DivisionAddNew = Vue.component('add-new-division', require('./components/division/AddDivision.vue'));

//holidays Manager
const HolidayManager = Vue.component('holiday-list',require('./components/holiday/ListHolidays.vue'));

var router = new VueRouter({
	routes: [
		{ path: '/', component: MainReport, name: 'index', props: true }, //get total worktime list project
		{ path: '/list-worktime', component: ListWorkTime, name: 'list-worktime', props: true }, //get list personal worktime by member,
		{ path: '/userprofile', component: UserProfile, name: 'userprofile', props: true }, //get list personal worktime by member,
	]
});

const app = new Vue({
    el: '#app',
	router,
});
