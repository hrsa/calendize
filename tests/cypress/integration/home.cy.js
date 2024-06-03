/// <reference types="cypress" />
describe("Home page", () => {
    context("Landing page", () => {
        beforeEach(() => {
            cy.visit({ route: "home" });
            cy.contains("All these").should("be.visible").and("contain.text", "All these important emails");
            cy.contains("All done").should("not.be.visible");
            cy.contains("Calendize them").should("have.text", "Calendize them!").wait(2000);
        });

        it("landing page is interactive", () => {
            cy.contains("Calendize them").click();
            cy.contains("All done").should("be.visible").and("contain.text", "All done! Easy, right?");
            cy.contains("All these").should("not.be.visible");
            cy.contains("Wait").should("have.text", "Wait, let's go back...");
            cy.contains("Wait").click();

            cy.contains("All these").should("be.visible").and("contain.text", "All these important emails");
            cy.contains("All done").should("not.be.visible");
            cy.contains("Calendize them").should("have.text", "Calendize them!").wait(2000);
        });

        it("can access generation page", () => {
            cy.contains("Calendize them").click();
            cy.contains("Cool! I'm in!").click().wait(1000);
            cy.location("pathname").should("eql", "/try");
        });
    });

    context("Try page", () => {
        beforeEach(() => {
            cy.visit({ route: "try" });
            cy.contains("Calendize and get by email").should("be.visible").and("be.disabled");
        });

        it("events generation works", () => {
            cy.artisan("migrate:fresh");
            let date = new Date();
            date.setDate(date.getDate() - 1);
            let yesterday = date.toISOString().split("T")[0];
            cy.create("App\\Models\\User", 1, {
                name: "Tester",
                email: "tester@example.com",
                password: "password",
                has_password: true,
                email_verified_at: yesterday,
            });

            cy.intercept("POST", "api/guest-generate-calendar").as("guestGenerate");
            cy.intercept("GET", "/verify-email").as("verifyRedirect");

            cy.contains("My users can simply forward").should("exist").and("be.visible");
            cy.wait(1000);
            cy.contains("My users can simply forward").click();
            cy.contains("My users can simply forward").parent().should("not.be.visible");

            cy.get("input").first().type("justanew@email.com");
            cy.contains("Calendize and get by email").should("be.visible").and("be.disabled");
            cy.get("input").first().clear();

            cy.get("textarea").first().type("a new event that i want to try out");
            cy.contains("Calendize and get by email").should("be.visible").and("be.disabled");

            cy.get("input").first().type("tester@example.com");
            cy.get("textarea").focus().wait(1000);
            cy.contains("You already have an account.").should("exist").and("be.visible");

            cy.get("input").first().clear().type("notAnEmail");
            cy.get("textarea").focus().wait(1000);
            cy.contains("Please enter a valid email address.").should("exist").and("be.visible");

            cy.get("input").first().clear().type("another@email.com");
            cy.get("textarea").focus().wait(1000);
            cy.contains("Calendize and get by email").should("be.visible").and("be.enabled");
            cy.contains("Calendize and get by email").wait(2000).click().wait("@guestGenerate");
            cy.wait("@verifyRedirect");
        });

        it("terms of service link works", () => {
            cy.contains("Terms of service").click();
            cy.location("pathname").should("eql", "/terms-of-service");
        });

        it("privacy policy link works", () => {
            cy.contains("Privacy policy").click();
            cy.location("pathname").should("eql", "/privacy-policy");
        });

        it("pricing button works", () => {
            cy.contains("Pricing").click();
            cy.location("pathname").should("eql", "/pricing");
        });

        it("login button works", () => {
            cy.contains("Start").click();
            cy.location("pathname").should("eql", "/login");
        });
    });
});
