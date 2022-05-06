import "./styles/app.scss";
import "./bootstrap";
import Vue from "vue";
import axios from "axios";
import Notepad from "./components/Notepad.vue";
import Notelist from "./components/Notelist.vue";
import { debounce } from "debounce";
import moment from "moment";

let blankNote = {
  id: 0,
  body: "",
  created_at: moment().format('DD-MM-YY HH:mm:ss'),
  updated_at: moment().format('DD-MM-YY HH:mm:ss'),
};

new Vue({
  el: "#app",
  components: { Notepad, Notelist },
  data() {
    return {
      note: blankNote,
      notes: [blankNote],
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
      // todo: save previous active note
      axios.post("/note/add").then((response) => {
        this.popNewNote(response.data.note);
      });
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
        this.note = response.data.notes[0];
      }
    });
  },
});
