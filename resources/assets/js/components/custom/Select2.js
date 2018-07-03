export default{
    props: ['data','current_value', 'default_option'],
    template: '<select/>',
    data () {
        return {
            current_val: this.current_value,
            data_val : this.data
        }
    },
    mounted: function () {
        var _self = this;
        
        _self.initData(_self.data_val, _self.$el);

        $(this.$el).select2().on("change", function(e){
            _self.$emit('getValue', $(this).val());
            _self.$emit('on_change', e);
        });
    },

    methods: {
        initData: function (appendData, el) {
            var _self = this;
            $(el).empty();
            
            $('<option/>',{
                selected: typeof _self.current_val === 'undefined' ? "selected" : false,
                text: _self.default_option
            }).appendTo(el);

            if (appendData.length > 0) {
                for (var i = 0; i < appendData.length; i++)
                {
                    $('<option/>', {
                        value:appendData[i].id,
                        text: appendData[i].name,
                        selected: parseInt(appendData[i].id) === parseInt(_self.current_val) ? "selected" : false,
                    }).appendTo(el);
                }
            }
        },
    },
    destroyed: function () {
        $(this.$el).select2('destroy');
    }
                        
}


