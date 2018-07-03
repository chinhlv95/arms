import { Pie } from 'vue-chartjs';

export default Pie.extend({
	props:['data','backgroundColor','label','legentArea'],
	data() {
		return {
			dataCompare: []
		}
	},
	mounted() {
		if(this.data) {
			this.renderPieChart();
			var html = this._chart.generateLegend();
			var _self = this;
			$(html).find('li').each(function(i,e){
				$("#" + _self.legentArea).append(e);
				$(e).on('click', function(){
					_self.labelClick(i, e);
				});
			});
		}
	},
	watch:{
		data: function(newData, oldData){
			if(oldData){
				var chart = this._chart;
				chart.destroy();
			    this.renderPieChart();
			}
			this.$emit('update-total-worktime', newData);
		}
	
	},
	methods: {
		renderPieChart: function(){
			var self = this;
			this.renderChart(
			{// data
				labels: this.label,
		        datasets: [{
		          label: "Manager Project (hours)",
		          backgroundColor: this.backgroundColor,
		          data: this.data,
		          borderWidth: 0.5,
		        }]
			}, { // option
				cutoutPercentage: 0,
				legend: {
		            display: false
		        },			    
			});
		},
		labelClick: function(i, e){
			var index = this.label.indexOf($(e).text());
			if(index >= 0 && this.data.length > 1){
				$(e).css('text-decoration','line-through');
				
				this.addDataCompare(this.data, this.backgroundColor, this.label, $(e).text(), i);
				
				this.backgroundColor.splice(index, 1);
				this.label.splice(index, 1);
				this.data.splice(index, 1);
			}
			else if(index >= 0 && this.data.length === 1){
				this.addDataCompare(this.data, this.backgroundColor, this.label, $(e).text(), i);
			}
			else{
				$(e).css('text-decoration','blink');
				var dataInsert = this.findData($(e).text());
				
				this.label.splice(dataInsert.index, 0, $(e).text());
				this.backgroundColor.splice(dataInsert.index, 0, dataInsert.color);
				this.data.splice(dataInsert.index, 0, dataInsert.data);
			}
		},
		addDataCompare: function(data, color, label, value, i){
			var info = {};// contain: index, color, data, label
			info.index = i;
			info.label = value;
			// find data
			for(var j = 0; j< label.length; j++)
			{
				if(label[j] === value){
					info.data = data[j];
					info.color = color[j];
					break;
				}
			}
			// add array
			if(this.dataCompare.length > 0)
			{
				for(var j = 0; j< this.dataCompare.length; j++){
					if(this.dataCompare[j].label === value){
						break;
					}
					else{
						this.dataCompare.push(info);
						break;
					}
				}
			}
			else{
				this.dataCompare.push(info);
			}
		},
		findData: function(label){
			for(var i = 0; i< this.dataCompare.length; i++)
			{
				if(this.dataCompare[i].label === label)
				{
					return this.dataCompare[i];
					break;
				}
			}
		}
	}
});