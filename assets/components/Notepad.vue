<template>
    <div class="notes__preview">
        <input v-model="noteTitle" class="notes__title" type="text" placeholder="Enter a title...">
        <textarea v-model="noteBody" class="notes__body" placeholder="Write stuff..."></textarea>
    </div>
</template>

<script>
export default {
    data() {
        return {
            noteTitle: '',
            noteBody: ''
        }
    }
}
</script>

<style>

</style>