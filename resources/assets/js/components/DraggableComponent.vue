<template>
    <div class="container">
        <div class="row" style="display: flex">
            <div class="col-6">
                <h3>Заявки</h3>
                <draggable class="list-group" v-for="(element, index) in list1" :key="element.id" group="people" @change="log" :move="checkMove">
                    <div class="list-group-item"
                         v-for="(iElement, iIndex) in list1[index]"
                         v-model="list1[index].iIndex"
                         :key="iElement.name">
                        {{ iElement.name }} {{ iIndex }}
                    </div>
                </draggable>
            </div>


            <div class="col-6">
                <h3>Машины</h3>
                <div class="container-list-dropable">
                    <draggable class="list-group list-dropable" v-for="(elementt, indexx) in list2" :key="elementt.id" group="people" @change="log">
                        <!--Ваз {{ list2.length }}-->
                        <div class="list-group-item"
                             v-for="(iElementt, iIndexx) in list2[indexx]"
                             v-model="list2[indexx].iIndexx"
                             :key="iElementt.name">
                            {{ iElementt.name }} {{ iIndexx }}
                        </div>
                    </draggable>
                    <!--<draggable class="list-group list-dropable" v-for="(elementt, indexx) in 4" :key="indexx" v-model="list2" group="people" @change="log">-->
                        <!--Ваз {{ list2.length }}-->
                    <!--</draggable>-->
                </div>
            </div>

            <div class="col-12">
                {{list1}}
                <br>
                {{list2}}
            </div>
        </div>
    </div>
</template>
<script>
    import draggable from 'vuedraggable'
    export default {
        components: {
            draggable
        },
        data() {
            return {
                list1: [
                    [
                        { name: "John", id: 1 },
                        { name: "Joao", id: 2 },
                        { name: "Jean", id: 3 },
                        { name: "Gerard", id: 4 }
                    ],
                    [
                        { name: "Дмитрий", id: 5 },
                        { name: "Иван", id: 6 },
                        { name: "Василий", id: 7 },
                        { name: "Артур", id: 8 }
                    ],
                ],
                list2: [
                    [
                        { name: "Кеша", id: 10 },
                    ],
                    [
                        { name: "Саша", id: 11 }
                    ],
                ],
            };
        },
        watch: {
            list1: {
                deep: true,
                handler(val, oldVal){
                    console.log('list1 changed');
                },
            },
            list2: {
                deep: true,
                handler(val, oldVal){
                    console.log('list2 changed');
                },
            },
            listSecond(newValue, oldValue) {
                console.log(1);
                console.log(newValue);
                console.log(oldValue);
                console.log(2);
            },
        },
        methods: {
            log: function(evt) {
                console.log(evt);
            },
            checkMove: function(evt){
                return ('moved', evt);
            },
        },
        computed: {
            /**
             * Функция для слежки от lodash
             *  это дает:
             *      1. глубокий смотр(видит изменения внутренних элементов тоже)
             *      2. отдает новые и старые значения
             * @returns {*}
             */
            listSecond() {
                return _.cloneDeep(this.list2);
            },
        },
        mounted(){
            console.log(this.list2);

            // let el = this;
            // setInterval(function() {
                // console.log('------');
                // console.log(el.list1);
                // console.log(el.list2);
                // el.list2[0] = [];
                // console.log(el.list2[0]);
                // console.log(el.list2);

                // el.list2 = [];

                // el.list2.push([]);
            // }, 1000);
        }
    };
</script>

<style>
    .col-6{
        overflow-x: scroll;
    }
    .list-group{
        display: flex;
        /*display: block;*/
        margin:20px 0;
    }
    .list-group .list-group-container{
        width:100%;
    }
    .list-group .list-group-container::after{
        content:' ';
        width:100%;
        display:block;
    }

    .list-group .list-group-item{
        padding:10px 20px;
        background-color:#fff;
        margin:0 3px;
        cursor:pointer;
    }

    .container-list-dropable {
        display:flex;
        margin:20px 0;
    }
    .list-dropable{
        padding:20px 30px;
        background-color:#ccc;
        margin:0 3px;
    }
</style>