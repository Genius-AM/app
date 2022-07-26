<template>
    <div class="container">
        <div class="row">
        <div class="form-group col-lg-4 col-md-4">
            <div class="check-item">
                <input type="radio" class="checkbox" id="payable" value="weekday" v-model="checked" name="is_payable" @click="switchWeekday">
                <label for="payable">По дню недели</label>
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-4">
            <div class="check-item">
                <input type="radio" class="checkbox" id="not_payable" value="date" v-model="checked" name="is_payable" @click="weekday = ''">
                <label for="not_payable">По дате</label>
            </div>
        </div>
        </div>

        <div v-if="checked === 'weekday'">
        <div class="row">
            <div class="form-group col-sm-12">
                <label>Выберите день недели</label>
                <select name="weekday" class="nice-select" v-model="weekday">
                <option value=''>Не выбрана</option>
                <option v-for="(weekday, index) in weekdays" :value="index" :key="index"> {{ weekday }}</option>
            </select>
            </div>
        </div>
        </div>

        <div v-if="checked === 'date'">
            <div class="row">
                <div class="form-group col-sm-12">
                    <label>Выберите дату</label>
                    <input type="date" v-model="selectedDate" class="custom-date-picker mini-input-date" @change="updateTimetable">
                </div>
            </div>
        </div>


        <timetable-part v-if="timetable"
                        :stages="timetable"
                        :weekday="weekday"
                        :date="selectedDate"
                        :route-car="this.routeCar"
                        @timetableUpdate="updateTimetable">
        </timetable-part>

    </div>
</template>

<script>
    import TimetablePart from "./TimetablePart.vue";
    import moment from 'moment';

    export default {
        props: ['routeCar'],
        components: {
            TimetablePart,
        },
        data() {
            return {
                timetables: [],
                timetable: null,
                weekdays: {
                    'monday' : 'Понедельник',
                    'tuesday' : 'Вторник',
                    'wednesday' : 'Среда',
                    'thursday' : 'Четверг',
                    'friday' : 'Пятница',
                    'saturday' : 'Суббота',
                    'sunday' : 'Воскресенье'
                },
                weekday : '',
                checked: "weekday",
                selectedDate: ''
            }
        },
        watch: {
            weekday : {
                handler() {
                    this.getTimetable();
                },
            },
        },
        mounted() {
            this.updateTimetable();
        },
        methods: {
            getTimetable() {
                this.timetable = this.timetables[this.weekday];
            },
            async updateTimetable() {
                this.timetables = [];

                await axios.get('/admin/cars/timetables/actual/' + this.routeCar.id, {
                    params: {
                        date: this.selectedDate
                    }
                }).then(response => {
                    this.timetables = response.data;
                }).catch(err => {
                    this.catchErrors(err)
                });
                await this.setWeekday();
                await this.getTimetable();
            },
            switchWeekday() {
                this.selectedDate = '';
                this.weekday = '';
                this.updateTimetable();
            },
            setWeekday() {
                if (this.selectedDate) {
                    this.weekday = moment(this.selectedDate).format('dddd').toLowerCase();
                }
            }
        },
    }
</script>

<style scoped>

</style>