export default{
   template: '<div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input readonly="true" :disabled="idResource === 2" type="text" class="pull-right" :id="datepickerId"></div>',
    props:['defaultDate','classArr', 'startDate', 'datepickerId', 'language', 'idResource'], // classArray : ['class1','class2','class....']
    data () {
        return {
            stDate: '',
        }
    },
    mounted: function(){
        this.initDatepicker();
    },
    beforeDestroy: function() {
        $(this.$el).datepicker('hide').datepicker('destroy');
    },
    watch: {
        startDate: function(val,oldVal){
            $(this.$el).datepicker('destroy');
            this.stDate = val;
            this.initDatepicker();
        }
    },
    methods: {
        initDatepicker: function () {
            var self = this;
            $(this.$el).find('input[type=text]').addClass(this.classArr.toString().replace(',',' '));
            $(this.$el).datepicker({
                defaultDate: typeof this.defaultDate != 'undefined' ? this.defaultDate : null,
                autoclose: true,
                todayBtn: "linked",
                todayHighlight: true,
                startDate: this.stDate.length > 0 ? this.stDate : this.startDate,
                forceParse: false,
                clearBtn: true,
                language: this.language,
            }).on('changeDate', function(e){
                //when date changed => emit one varible update-date to parent component
                self.$emit('update-date', e.format(e,this.dateFormat));
            });

            if(typeof this.defaultDate != 'undefined'){
                $(this.$el).find('input').val(this.defaultDate);
            }
        }
    }
};