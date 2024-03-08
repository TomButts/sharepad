<style scoped>
 .remove-participant {
   float: right;
 }
</style>

<template>
  <div>
    <Modal>
      <div class="share__note">
        <div id="email-input">
          <input placeholder="Email address" />
          <button type="button">Invite</button>
        </div>
        <ul id="share-email-list">
          <template v-for="(participant) in participants">
            <li v-bind:key="participant.id">{{ participant.email }}<span class="remove-participant">&times;</span></li>
          </template>
        </ul>
      </div>
    </Modal>
  </div>
</template>

<script>
  import Modal from "../components/Modal.vue";

  export default {
    props: ["participants"],
    components: { Modal },
    methods: {
      addParticipant: function (email) {
        this.$emit("add-participant", email);
      },
      removeParticipant: function (id) {
        this.$emit("remove-participant", id);
      },
    },
  };
</script>
