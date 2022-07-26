<template>
    <draggable class="dragArea"  tag="div" :list="tasks" :group="{ name: 'g1', pull:true, put:true }">
        <div v-for="el in tasks" :key="el.name">
            <span>{{ el.name }}</span>
            <nested-draggable :tasks="el.tasks" />
        </div>
    </draggable>
</template>


<script>
    import draggable from 'vuedraggable'
    export default {
        props: ['tasks', 'parentGroup', 'state'],
        components: {
            draggable
        },
        mounted(){
        },
        name: "nested-draggable"
    }
</script>

<style scoped>
    .dragArea {
        height:100%;
        width:100%;
        /*outline: 1px solid;*/
    }
</style>