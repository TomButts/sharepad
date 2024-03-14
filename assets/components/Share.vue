<style scoped>
  .remove-participant {
    float: right;
  }

  .participant-email {
    display: inline-block;
    margin-right: 8px;
  }

  .share-email-list {
    list-style-type: none;
    padding-left: 0;
  }

  .share__note {
    padding: 16px;
  }

  .email-input-container {
    display: flex;
    padding-bottom: 0.5rem;
    justify-content: space-between;
    gap: 8px;
  }

  .email-input-container::after {
    content: "";
    flex: auto;
  }

  #share-email-input {
    flex: 4;
  }
  /* todo: add secondary flexible button style to app.scss - notes__button*/
</style>

<template>
  <div>
    <Modal :visible="visible" v-on:close-modal="closeModal()">
      <template v-slot:header>
        Share Note
      </template>
      <template v-slot:body>
        <div class="share__note">
          <div class="email-input-container">
            <input id="share-email-input" placeholder="Email address" v-model="email">
            <button id="share-email-button" class="button" type="button" @click="addParticipant">Invite</button>
          </div>
          <ul class="share-email-list">
            <template v-if="note.hasOwnProperty('participants') && note.participants.length > 0">
              <template v-for="(participant) in note.participants">
                <li v-bind:key="participant.id">
                  {{ participant.email }}<span class="remove-participant" @click="removeParticipant(participant.email)">&times;</span>
                </li>
              </template>
            </template>
            <template v-else>
              <div>This note is currently not shared with anyone!</div>
            </template>
          </ul>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
  import Modal from "../components/Modal.vue";

  export default {
    props: ["note", "visible"],
    data() {
      return {
        email: ''
      }
    },
    components: { Modal },
    methods: {
      addParticipant: function () {
        // todo: axios request.then(below code, or validation error)
        // todo: think about sharing with non registered users
        // todo: maybe flash a message about it being successful
        if ('' === this.email) {
          return;
        }

        this.note.participants.push({email: this.email})

        this.email = '';
      },
      removeParticipant: function (participantEmail) {
        this.note.participants = this.note.participants
          .filter(participant => participant.email !== participantEmail)

        // todo: axios to validate and remove. then below
        this.note.participants

      },
      closeModal: function () {
        this.$emit("close-share-modal")
      }
    },
  };
</script>
