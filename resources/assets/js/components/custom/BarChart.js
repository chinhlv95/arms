import { Bar } from 'vue-chartjs';

export default Bar.extend({
	props: ['labels','datasets', 'label'],// label is all days in week
	watch:{
		datasets: function(newData, oldData){
			var chart = this._chart;
			chart.destroy();
			this.renderBarChart();
		}
	},
	mounted () {
		this.renderBarChart(this.labels);
	},
	methods:{
		renderBarChart: function(){
			var _self = this;
			
			this.renderChart({
				
				labels: this.labels,
		        datasets: [
		          {
		            label: this.label,
		            backgroundColor: '#f87979',
		            data: this.datasets
		          }
		        ]
		    }, 
	    	{//config
	    		responsive: true, 
	    		maintainAspectRatio: false,
	    		legend: {
		            display: false
		        },
		        tooltips: {
		            enabled: true
		        },
		        hover: {
		            animationDuration: 0
		        },
		        layout: {
		            padding: {
		                top: 30
		            }
		        },
		        animation: {
		        	onComplete: function(animation) {
		        		// render the value of the chart above the bar
		        	    var ctx = this.chart.ctx;
		        	    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
		        	    ctx.fillStyle = this.chart.config.options.defaultFontColor;
		        	    ctx.textAlign = 'center';
		        	    ctx.textBaseline = 'bottom';
		        	    this.data.datasets.forEach(function (dataset) {
		        	        for (var i = 0; i < dataset.data.length; i++) {
		        	            var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
		        	            ctx.fillText(dataset.data[i] + ' h', model.x, model.y - 5);
		        	        }
		        	    });
                    }
		        }
	    	})
		}
	}
});