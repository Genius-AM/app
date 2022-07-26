<template>
    <div>
        <div class="row">
            <div class="form-group col-sm-12">
                <label> Время:</label>
                <input type="time" v-model="newTodoText" placeholder="Добавить" class="custom-date-picker">
                <button v-on:click.stop.prevent="addNewTodo" class="btn btn-green">Добавить</button>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-12">
                <label class="label-control">Расписание:</label>
            </div>
        </div>
        <div class="row">
            <div v-for="(stage, index) in phase" class="form-group col-lg-3">
                <label class="label-control">{{ index + 1 }}:</label>
                <input type="time" name="stages[]" class="custom-date-picker" :value="stage.time" @change="changeDate(index, $event)">
                <button v-on:click.stop.prevent="deletePhase(index, stage.id)" class="btn btn-dark"><i class="fa fa-times-circle"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="float-right">
                    <button type="submit" class="btn btn-green" @click="saveTimetable()">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            //
        },
        props: ['stages', 'weekday', 'date', 'routeCar'],
        data() {
            return {
                phase: [],
                deleted: [],
                newTodoText: '',
                nextTodoId: 0,
            }
        },
        mounted() {
            this.createPhase();
        },

        watch: {
            stages: {
                handler() {
                    this.createPhase();
                },
                deep: true
            }
        },
        computed: {

        },
        methods: {
            createPhase() {
                if (this.stages) {
                    var stages = [];
                    _.each(this.stages, function(stage) {
                        stages.push({time : stage.time.split(' ')[0], id : stage.id, changed : false});
                    });
                    this.phase = stages;
                }
            },
            addNewTodo() {
                if (this.newTodoText) {
                    this.phase.push({time : this.newTodoText + ':00', id : null, changed : true});

                    this.newTodoText = ''
                }
            },
            changeDate(index, e) {
                this.phase[index].time = e.target.value + ':00';
                this.phase[index].changed = true;
            },
            async deletePhase(index, id) {
                if (id) {
                    if (await this.checkIfCanDelete(id)) {
                        this.deleted.push(this.phase.splice(index, 1)[0]);
                    }
                } else {
                    this.phase.splice(index, 1)
                }
            },
            async checkIfCanDelete(id) {
                let check = false;

                await axios.get('/admin/cars/timetables/check/' + id).then(response => {
                    check = true;
                }).catch(err => {
                    this.catchErrors(err)
                });

                return check;
            },
            saveTimetable() {
                axios.post('/admin/cars/timetables/save/' + this.routeCar.id, {
                    phase: this.phase,
                    deleted: this.deleted,
                    weekday: this.weekday,
                    date: this.date
                }).then(response => {
                    this.notifyResponse(response);
                    this.$emit('timetableUpdate');
                }).catch(err => {
                    this.catchErrors(err)
                });
            }
        }
    }
</script>
