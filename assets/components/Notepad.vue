<template>
    <div class="notes__preview">
        <input v-model="note.title" class="notes__title" type="text" placeholder="Enter a title...">
        <textarea v-model="note.body" class="notes__body" placeholder="Write stuff..."></textarea>
    </div>
</template>

<script>
export default {
    props: ['note']
}
</script>