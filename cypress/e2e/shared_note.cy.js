describe('Note sharing and real time note updates test', () => {
  before(() => {
    cy.request('/login')
      .its('body')
      .then(body => {
        const $html = Cypress.$(body)
        const csrf = $html.find('input[name=_csrf_token]').val()

        cy.loginByCSRF(csrf).then(resp => {
          expect(resp.status).to.eq(200)
        })
      })

    cy.fixture('notes.json').then(notes => {
      cy.intercept('GET', '/notes', notes)

      cy.visit('/')

      cy.intercept('POST', '/note/save', req => {
        req.reply({
          status: 200,
          body: {
            notes: notes[0]
          }
        })
      }).as('saveNote')

      cy.wait('@saveNote').then(interception => {

      })
    })
  })

  it('Lets user open the share modal', () => {
    cy.get('.share-button').eq(0).click()
    cy.get('.share-note').should('be.visible')
  })

  it('Allows you to add a participant', () => {
    const sharedUserEmail = 'test1@test.com'

    cy.get('#share-email-input').click().type(sharedUserEmail)

    cy.get('#share-email-button').click()

    cy.get('.share-email-list').contains(sharedUserEmail).should('exist')
  })

  it('It allows you to remove a participant', () => {
    cy.get('.remove-participant').eq(0).click()
    cy.get('.share-email-list').contains('test1@test.com').should('not.exist')
  })

  it('It allows you to close the share modal', () => {
    cy.get('.modal-content').should('be.visible')
    cy.get('.modal-backdrop').click({ force: true })
    cy.get('.modal-content').should('not.be.visible')
  })

  /**
   * Put on hold due to cypress not supporting Event source @see https://github.com/cypress-io/cypress/issues/2747
   *
   * I will try the workaround but I don't like the idea of adding that listener to the main window.
   */
  // it("It automatically updates shared notes - detecting changes from publisher url", () => {
  // });
})
