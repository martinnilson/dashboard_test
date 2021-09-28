<template>
    <div class="container-fluid">
        <div id="head" class="row">
            <div class="col-12 pt-3 pb-3 bg-dark">
                <h2 class="text-light mb-0">Sales dashboard</h2>
            </div>
        </div>
        <div v-if="error.status" id="error" class="row">
            <div class="position-fixed bg-danger pt-1 pb-1">
                <div v-html="error.message" class="p mb-0 text-light d-inline-block"></div>
                <button @click="error.status = false" class="btn btn-dark mt-1">Close</button>
            </div>
        </div>
        <div v-if="loading.status && loading.key === 'main'" id="main" class="row">
            <div class="col-12 pt-3">
                <p class="text-center">Loading</p>
            </div>
        </div>
        <div v-else id="main" class="row">
            <div class="col-12 pt-3">
                <div v-if="database_populated" id="dashboard" class="container-fluid pl-3 pr-3">
                    <div class="row">
                        <div class="col-8">
                            <highcharts :options="chart_options"></highcharts>
                        </div>
                        <div class="col-4">
                            <div>
                                <div class="form-group">
                                    <label for="date_start">Start date</label>
                                    <input 
                                        id="date_start"
                                        v-model="date_start"
                                        :min="moment.tz(date_now,'utc').subtract(1,'year').format('YYYY-MM-DD')"
                                        :max="date_end"
                                        type="date"
                                        class="form-control"
                                        placeholder="Start date">
                                </div>
                                <div class="form-group">
                                    <label for="date_end">End date</label>
                                    <input
                                        id="date_end" 
                                        v-model="date_end"
                                        :min="date_start"
                                        :max="moment.tz(date_now,'utc').format('YYYY-MM-DD')"
                                        type="date"
                                        class="form-control"
                                        placeholder="End date">
                                </div>
                                <button :disabled="loading.status && loading.key === 'dates'" @click="fetchData('order','index')" class="btn btn-dark mt-2 w-100">
                                    <span v-if="loading.status && loading.key === 'fetch_data'">Loading...</span>
                                    <span v-else>Submit</span>
                                </button>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" v-model="checkbox_phone" @change="populateChart()" id="phone">
                                <label class="form-check-label" for="phone">
                                    Phone
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="checkbox_pad" @change="populateChart()" id="pad">
                                <label class="form-check-label" for="pad">
                                    Pad
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="checkbox_desktop" @change="populateChart()" id="desktop">
                                <label class="form-check-label" for="desktop">
                                    Desktop
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else id="populate_database" class="container-fluid pl-3 pr-3 mt-5">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h5>Hello there!</h5>
                            <p>Before we can look at charts we need to populate the tables in the database. Press the button below!</p>
                            <button :disabled="loading.status && loading.key === 'populate'" @click="populateDatabase()" class="btn btn-dark" :style="{'width':'200px'}">
                                <span v-if="loading.status && loading.key === 'populate'">Loading...</span>
                                <span v-else>Populate</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const axios = require('axios')
const moment = require('moment-timezone')
import {Chart} from 'highcharts-vue'

export default {
    components: {
        highcharts: Chart 
    },
    data() {
        return{
            chart_data: [],
            chart_options: {
                title: {
                    text: "Sales"
                },
                series: [{
                    name: "Revenue",
                    data: []
                }],
                xAxis: {
                    title: {
                        text: "Date"
                    },
                    type: "datetime"
                },
                yAxis: {
                    title: {
                        text: "Revenue (SEK)"
                    },
                }
            },
            checkbox_desktop:true,
            checkbox_pad:true,
            checkbox_phone:true,
            database_populated: true,
            date_start: '',
            date_now: Date.now(),
            date_end: '',
            error: {status: false, message: ''},
            loading: {status: true, key:'main'},
            moment: moment,
        }
    },
    computed:{
        date_start_init: function () {
            return this.moment.tz(this.date_now,'utc').subtract(30,'days').format('YYYY-MM-DD');
        },
        date_end_init: function () {
            return this.moment.tz(this.date_now,'utc').format('YYYY-MM-DD');
        }
    },
    created() {
        this.date_start = this.date_start_init
        this.date_end = this.date_end_init
        this.checkDatabase()
    },
    methods:{
        checkDatabase(){
            axios.get('api/index.php?database=check')
            .then(response => {
                //console.log(response)
                if(response.data){
                    this.fetchData('order','index')
                } else {
                    this.database_populated = false
                }
                this.loading.status = false
            })
            .catch(error => {
                //console.log(error)
            })
        },
        fetchData(controller, method){
            this.loading.key = 'fetch_data'
            this.loading.status = true
            axios.get('api/index.php?controller='+controller+'&method='+method+'&date_start='+this.date_start+'&date_end='+this.date_end )
            .then(response => {
                //console.log(response)
                this.chart_data = response.data
                this.populateChart()
                this.loading.status = false
            })
            .catch(error => {
                //console.log(error)
                this.loading.status = false
            })
        },
        populateChart(){
            let chart_data_output = []
            for(let i in this.chart_data){
                if(!this.checkbox_desktop && this.chart_data[i].device == "Desktop" || !this.checkbox_pad && this.chart_data[i].device == "Pad" || !this.checkbox_phone && this.chart_data[i].device == "Phone"){
                    continue;
                }

                if(chart_data_output[this.chart_data[i].created_at] == undefined){
                    chart_data_output[this.chart_data[i].created_at] = 0
                }
                chart_data_output[this.chart_data[i].created_at] += this.chart_data[i].order_items_price
            }
            let count = 0
            this.chart_options.series[0].data = []
            for(let el in chart_data_output){
                this.chart_options.series[0].data[count]= [Date.parse(el), chart_data_output[el]]
                count++
            }
        },
        populateDatabase(){
            this.loading.key = 'populate'
            this.loading.status = true
            axios.get('api/index.php?database=populate')
            .then(response => {
                //console.log(response.data)
                if(response.data === "done"){
                    this.database_populated = true
                    this.fetchData('order', 'index')
                } else {
                    this.error = {status:true, message:response.data}
                }
                this.loading.status = false
            })
            .catch(error => {
                //console.log(error)
                this.loading.status = false
            })
        }
    }
}

</script>