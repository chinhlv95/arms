export default{
   template: "<span><i class='fa fa-calendar fa-2x' aria-hidden='true'></i></span>",
   props:['defaultDate', 'language'],
    mounted: function(){
        var self = this;
        $(this.$el).datepicker({
            autoclose: true,
            todayBtn: "linked",
            todayHighlight: true,
            endDate: new Date(),
            language: this.language
        }).on('changeDate', function(e){
            //when date changed => emit one varible update-date to parent component
            self.$emit('update-date', e.format(e,this.dateFormat));
        });
    },
    beforeDestroy: function() {
        $(this.$el).datepicker('hide').datepicker('destroy');
    }
};