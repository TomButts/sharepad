import "./styles/app.scss";
import "./bootstrap";
import Vue from "vue";
import axios from "axios";
import Notepad from "./components/Notepad.vue";
import Notelist from "./components/Notelist.vue";
import Share from "./components/Share.vue";
import { debounce } from "debounce";
import moment from "moment";

let blankNote = {
  id: 0,
  body: "",
  created_at: moment().format('DD-MM-YY HH:mm:ss'),
  updated_at: moment().format('DD-MM-YY HH:mm:ss'),
};

// dev stub: remove in next phase: adding real verified secure participants
const participants = [
  {
    id: 101,
    email: 'buddy.guy@friend.com'
  },
  {
    id: 202,
    email: 'not.your@buddy.com'
  }
];

new Vue({
  el: "#app",
  components: { Notepad, Notelist, Share },
  data() {
    return {
      note: blankNote,
      notes: [blankNote],
      participants: participants
    };
  },
  methods: {
    changeNote: function (id) {
      const selectedNoteIndex = this.notes.findIndex((note) => {
        return note.id === id;
      });

      // todo: save final state of previous active note
      let selectedNote = this.notes[selectedNoteIndex];

      // move element to front of array
      this.notes.splice(selectedNoteIndex, 1);

      this.popNewNote(selectedNote);
    },
    popNewNote: function (note) {
      this.notes.unshift(note);
      this.note = note;
    },
    newNote: function () {
      if (this.note.body.length > 0) {
        axios
          .post("/note/save", {
            id: this.note.id,
            body: this.note.body,
          })
          .then((response) => {
            this.popNewNote(blankNote);
          });
      }
    },
  },
  watch: {
    note: {
      handler: debounce(function (e) {
        if ("" === this.note.body) {
          return;
        }

        axios
          .post("/note/save", {
            id: this.note.id,
            body: this.note.body,
          })
          .then((response) => {
            if (0 === this.note.id && 0 !== response.data.note) {
              this.note.id = response.data.note.id;
            }
          });
      }, 500),
      deep: true,
    },
  },
  mounted() {
    axios.get("/notes").then((response) => {
      if (0 !== response.data.notes.length) {
        this.notes = response.data.notes;
        
        if (response.data.notes && response.data.notes.length > 0) {
          this.note = response.data.notes[0];
        }
      }
    });
  },
});
