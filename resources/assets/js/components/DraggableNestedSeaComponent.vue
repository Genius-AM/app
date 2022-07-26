<template>
    <div>
        <div class="gelen__main__wrapper">
            <div class="gelen__left__wrapper">
                <h3 class="text-center">Заявки менеджеров</h3>
                <draggable v-if="managers.length > 0" ghost-class="ghost" class="ndragArea"  tag="div" :list="managers" :group="{ name: 'g1', pull:false, put:false }" :sort="false">
                    <div v-for="(el, key, index) in managers" :key="index" v-if="el.tasks.length > 0" class="new_gelen__manager__wrap">
                        <div class="gelen__manager__name__wrap">
                            <h4>{{ el.region }}</h4>
                            <h5>{{ el.name }}</h5>
                            <h5>{{ el.point }}</h5>
                        </div>

                        <draggable ghost-class="ghost" class="new_dragArea" tag="div" :list="el.tasks" :group="{ name: 'g1', pull:true, put:false }" :sort="false">
                            <div v-for="el1 in el.tasks" class="new_gelen__order__table__wrap gelen__order__table__wrap gelen_manager_order" :key="el1.id"
                                :class="{
                                 jeep__main__wrapper_decline: el1.status_id === 5 || el1.status_id === 8
                                }">

                                <div class="gelen__address__time__block">
                                    <span v-if="el1.food == 1" class="rest_span">
                                        <img src="/images/ic_restaurant_24px.svg" alt="">
                                    </span>
                                    <span class="address_span" :title="getAddressWithPrepayment(el1.address, el1.prepayment)" v-text="getAddress(el1.address, 10)"></span>
                                    <div class="time_span date__time__wrapper">
                                        <span>{{ momentTime(el1.time) }}</span>
                                    </div>
                                </div>

                                <div class="gelen__address__time__block">
                                    <span class="route_span" v-text="getShortName(el1.route)"></span>
                                    <span class="comment_span" :title="el1.client.comment" v-text="getAddress(el1.client.comment, 9)"></span>
                                    <div class="date__time__wrapper">
                                        <span class="date">{{ momentShort(el1.date) }}</span>
                                    </div>
                                </div>

                                <div class="gelen__pax__edit__status__wrapper">
                                    <div class="gelen__pax__wrap">
                                        <div :class="routeColor(el1.route)">{{ el1.men }}</div>
                                        <div :class="routeColor(el1.route)">{{ el1.women }}</div>
                                        <div :class="routeColor(el1.route)">{{ el1.kids }}</div>
                                    </div>
                                    <div class="amount_apassengers">{{getAmountOFPassengers(el1.men, el1.women, el1.kids)}}</div>
                                    <div class="edit__wrap">
                                        <div class="payment__wrap">
                                            <div class="phone-block-order">
                                                <div>{{ getCleanText( el1.client.phone )}}</div>
                                                <div>{{getCleanText( el1.client.phone_2 )}}</div>
                                            </div>
                                            <div class="money-block"><strong>{{ el1.prepayment }}</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </draggable>
            </div>
        </div>
    </div>
</template>
<script>
    import draggable from 'vuedraggable'
    import nestedDraggable from "./Nested";

    export default {
        props: ['managers'],
        components: {
            draggable,
            nestedDraggable,
        },
        data() {
            return {
                idDispatcherOrder: '',
            };
        },
        methods: {
            /**
             * Возвращает сумму принятые объектов
             */
            getAmountOFPassengers(men, women, kids){
                return (men+women+kids);
            },
            /*
             * Убираем лишние пробелы с текста
             */
            getCleanText(text){
                let new_text = '';
                if(text != null){
                    new_text = text.replace(/ /g, '');
                    new_text = new_text.replace(/\(/g, '');
                    new_text = new_text.replace(/\)/g, '');
                    new_text = new_text.replace(/-/g, '');
                }
                else new_text = '';

                return new_text;
            },
            /**
             * Возвращает буквенные сокращения
             */
            getShortName(text){
                if (typeof text === 'object' && text != null) {
                    if (text.name.length > 0) {
                        let result = '';
                        let arr_text = text.name.split(' ');
                        for (let i = 0; i < arr_text.length; i++) {
                            result += arr_text[i].substr(0, 1);
                        }
                        return result.toUpperCase();
                    } else return '';
                } else return text;
            },
            /*
             * Режет строку с адресов
             */
            getAddress(text, length) {
                if (typeof text === 'object' && text != null) {
                    if (text.name != null && text.name.length > length) {
                        let cut_str = text.name.substring(0, length + 1);
                        return cut_str.trim() + '...';
                    } else return text.name;
                }

                if (text != null && text.length > length) {
                    let cut_str = text.substring(0, length + 1);
                    return cut_str.trim() + '...';
                } else return text;
            },
            routeColor(route) {
                if (route != null) {
                    return route.color;
                }
            },
            /**
             * Возврат адреса + предоплаты
             * формат: "ул. Весеняя, д.1, предоплата 300"
             */
            getAddressWithPrepayment(address, prepayment) {
                if (typeof address === 'object' && address != null) {
                    return `${address.name}, предоплата ${prepayment}`;
                }

                return `${address}, предоплата ${prepayment}`;
            },
        },
    };
</script>

<style lang="scss" scoped>
    .sortable-chosen{
        position: relative !important;
        z-index: 9999 !important;
        /*background-color:red !important;*/
        opacity:1 !important;
        .gelen__address__time__block, .gelen__pax__edit__status__wrapper{
            position: relative !important;
            z-index: 9999 !important;
            top: 0;
            left: 0;
        }
    }

    .lastDragArea{
        /*position:relative;*/
        /*z-index: 5;*/
    }
    .lastDragArea{
        .sortable-chosen{
            position: relative;
            z-index: 10;
        }
    }
    .gelen__address__time__block {
        height: 36px;

        .route_span {
            padding: 5px 2px;
        }
    }
    .gelen__pax__edit__status__wrapper {
        height: 58px;

        .payment__wrap {
            height: 56px;

            .phone-block-order {
                height: 100%;
            }

            .money-block {
                align-items: stretch;
            }
        }
    }
</style>