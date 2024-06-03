describe("login/register page", () => {
    context("register", () => {
        before(() => {
            cy.artisan("migrate:fresh");
        });

        beforeEach(() => {
            cy.visit({ route: "login" });
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login / Register");
            cy.contains("button", "Register");
        });

        it("can register a new user", () => {
            cy.get("#email").type("tester@example.com");
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Register");
            cy.contains("button", "Register").click().wait(2000);
            cy.contains("The password field is required.").should("exist");
            cy.contains("The name field is required.").should("exist");
            cy.get("#password").type("password");
            cy.contains("button", "Register").click().wait(2000);
            cy.contains("The password field is required.").should("not.exist");
            cy.contains("The name field is required.").should("exist");
            cy.get("#name").type("Tester of Tests");
            cy.get("#password").type("password");

            cy.contains("button", "Register").click().wait(4000);
            cy.location("pathname").should("eql", "/verify-email");
        });

        it("doesn't show a register form when email exists", () => {
            cy.get("#email").type("tester@example.com").wait(3000);
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login");
            cy.get("#name").should("not.exist");

            cy.contains("button", "Register").should("not.exist");
        });

        it("shows errors for incorrect emails", () => {
            cy.get("#email").type("not even an email").wait(1000);

            cy.contains("button", "Register").click().wait(1000);
            cy.contains("The email field must be a valid email address.");
        });
    });
    context("login", () => {
        before(() => {
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
        });

        beforeEach(() => {
            cy.visit({ route: "login" });
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login / Register");
            cy.contains("button", "Register");
        });

        it("login with correct credentials", () => {
            cy.get("#email").type("tester@example.com").wait(1500);
            cy.get("#password").focus();
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login");
            cy.get("#password").type("password");

            cy.contains("button", "Log in").click().wait(4000);
            cy.location("pathname").should("eql", "/dashboard");
        });

        it("can not login with incorrect credentials, forgot password", () => {
            cy.get("#email").type("tester@example.com").wait(1500);
            cy.get("#password").focus();
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login");
            cy.get("#password").type("password123");

            cy.contains("button", "Log in").click().wait(4000);
            cy.location("pathname").should("eql", "/login");
            cy.contains("These credentials do not match our records.").should("exist");

            cy.contains("Forgot your password").click().wait(2000);
            cy.location("pathname").should("eql", "/forgot-password");
        });
    });

    context("static links", () => {
        beforeEach(() => {
            cy.visit({ route: "login" });
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login / Register");
            cy.contains("button", "Register");
        });

        it("terms of service link", () => {
            cy.contains("Terms of service").click();
            cy.location("pathname").should("eql", "/terms-of-service");
        });

        it("privacy policy link", () => {
            cy.contains("Privacy policy").click();
            cy.location("pathname").should("eql", "/privacy-policy");
        });
    });
});
