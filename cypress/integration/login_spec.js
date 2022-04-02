import user from "../fixtures/user.json";

describe("Test Login Features", () => {
  it("login page displays (when unauthenticated)", () => {
    // index should redirect to login page
    cy.visit("/");
    cy.url().should("include", "/login");

    cy.contains("Email address");
    cy.contains("Password");
    cy.contains("Sign in");
  });

  it("user can sign in", () => {
    cy.visit("/");
    cy.get("#email").type(user.email).should("have.value", "test@test.com");
    cy.get("#password").type(user.password);
    cy.contains("Sign in").click();
    cy.url().should("include", "/");
    cy.contains("Add Note");
  });

  it("logs out", () => {
    cy.visit("/logout");
    cy.contains("Email address");
    cy.contains("Password");
    cy.contains("Sign in");
  });
});
