<template>
    <div class="notes__preview">
        <textarea v-model="note.body" class="notes__body" placeholder="Write stuff..."></textarea>
    </div>
</template>

<script>
export default {
    props: ['note']
}
</script>