export default{
	props: ['data', 'id', 'placeholder', 'keyName', 'selected'],
	template: '<select/>',
	data() {
		return {
			condition: {'key': '', 'data': []}
		}
	},
	mounted: function(){
		var self = this;
		$(this.$el).select2({
			closeOnSelect: false,
			templateResult: this.formatState,
			placeholder: this.placeholder,
		}).on('change', function(e){
			self.$emit('add-condition', $(this).val());
		});		
		// add id for this element
		$(this.$el).attr('id', this.id);
		//set default selected item
		$('<option/>', {
			text: this.placeholder + ' (' + self.selected.length +')',
			disabled: true,
			selected:  true
		}).appendTo(self.$el);	

		$(this.$el).on('select2:open', function(e){
			self.appendData(self.data, self.$el);
		});
		
		$(this.$el).on('select2:close', function(e){
			$('<option/>', {
				text: self.placeholder + ' (' + self.selected.length +')',
				disabled: true,
				selected:  true
			}).appendTo(self.$el);	
		});

		$(document).on('mouseup, click', '.select2-results ul li', function(){
			$(this).attr('aria-selected', 'false');
		});
	},
	watch:{
		selected: function(){
			var self = this;
			$('<option/>', {
				text: self.placeholder + ' (' + self.selected.length +')' ,
				disabled: true,
				selected:  true
			}).appendTo(self.$el);
		}
	},
	methods: {
		formatState: function(state) {
		    if (!state.id) {
		        return state.text;
		    }
		    if(state.id != this.placeholder){
		    	var $state = $('<div style="margin: -6px -12px"><label style="width: 100%; margin-bottom: 0; padding: 10px 15px" for="' + state.id + '" > <input style="margin-right: 10px; vertical-align: middle; margin-top: -2px" ' + ( $.inArray(parseInt(state.id), this.selected) !== -1 ? "checked" : '')  + ' type="checkbox" id="' + state.id + '"/>' + state.text + '</label></div>');
		    }
		    return $state;
		},
		appendData: function(data, e){
			$(e).empty();

			$('<option/>', {
	            text: this.placeholder,
	            disabled: true,
	            selected:  true
			}).appendTo(e);		

			for(var i = 0; i < data.length; i++){
				$('<option/>', {
		            value: data[i].key,
					text: data[i].value
		        }).appendTo(e);
			}
		}
	}
}