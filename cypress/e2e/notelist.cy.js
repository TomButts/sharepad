describe('Notepad tests', () => {
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
  })

  it('has empty notes list item', () => {
    // have notes return empty
    cy.intercept('GET', '/notes', { notes: [] })

    cy.visit('/').contains('Add Note')

    cy.contains('New Note')
    cy.contains('No additional text')
  })
})
