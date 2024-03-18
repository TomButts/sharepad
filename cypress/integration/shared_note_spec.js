describe("Note sharing and real time note updates test", () => {
    before(() => {
      cy.request("/login")
        .its("body")
        .then((body) => {
          const $html = Cypress.$(body);
          const csrf = $html.find("input[name=_csrf_token]").val();

          cy.loginByCSRF(csrf).then((resp) => {
            expect(resp.status).to.eq(200);
          });
        });
    });

    // todo:
      // share button shows showShareModal
      // share button add participant works

    // todo: now that the note is successfully shared use monitor sessions to check shared note mercure updates
    // are working

    // you will need to mock the mercure hub request that publishes the update as cypress doesn't support multiple concurrent users

    // share button remove participant works
    // expect note to dissapear from notes list
  });