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

  .share-note {
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
</style>

<template>
  <div>
    <Modal :visible="visible" v-on:close-modal="closeModal()">
      <template v-slot:header>Share Note</template>
      <template v-slot:body>
        <div class="share-note">
          <div class="email-input-container">
            <input id="share-email-input" placeholder="Email address" v-model="email">
            <button id="share-email-button" class="button" type="button" @click="addParticipant">Invite</button>
          </div>
          <div class="email-input-err error" v-if="showError">
            Please enter a valid email address!
          </div>
          <ul class="share-email-list">
            <template v-if="note.hasOwnProperty('participants') && note.participants.length > 0">
              <template v-for="(participant) in note.participants">
                <li v-bind:key="participant.id">
                  {{ participant.email }}<span class="remove-participant" @click.stop="removeParticipant(note.id, participant.email)">&times;</span>
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
  import { emailValidationMixin } from '../Mixins/EmailValidationMixin.js';
  import axios from "axios";

  export default {
    mixins: [emailValidationMixin],
    props: ["note", "visible"],
    data() {
      return {
        email: '',
        showError: false
      }
    },
    components: { Modal },
    methods: {
      addParticipant: function () {
        if ('' === this.email || !this.$isValidEmail(this.email)) {
          this.showError = true;

          return;
        }

        this.showError = false;

        axios.post("/note/participant/add", {
          note_id: this.note.id,
          email: this.email,
        })
        .then((response) => {
          this.note.participants.push({email: this.email})

          this.email = '';
        }).catch((error) => {
          // todo: handling
        });
      },
      removeParticipant: function (noteId, participantEmail) {
        axios.post("/note/participant/remove", {
          note_id: noteId,
          email: participantEmail,
        })
        .then((response) => {
          this.note.participants = this.note.participants
            .filter(participant => participant.email !== participantEmail)
        }).catch((error) => {
          // todo: handling
        })
      },
      closeModal: function () {
        this.$emit("close-share-modal")
      }
    },
  };
</script>
