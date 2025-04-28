describe("profile page", () => {
    let date = new Date();
    date.setDate(date.getDate() - 1);
    let yesterday = date.toISOString().split("T")[0];
    const name = "Tester Of Tests";
    const credits = 5;
    const email = "testing@test.com";
    const password = "a-secret-password";

    beforeEach(() => {
        cy.artisan("migrate:fresh");
        cy.login({
            name: name,
            email: email,
            password: password,
            credits: credits,
            has_password: true,
            email_verified_at: yesterday
        });
        cy.visit({ route: "profile.edit" });
        cy.contains(credits + " credits remaining")
            .should("exist")
            .and("be.visible");
        cy.contains("button", name).should("exist").and("be.visible");
        cy.contains("h1", "Profile").should("exist").and("be.visible");
        cy.contains("Pulse").should("not.exist");
        cy.contains("Horizon").should("not.exist");
        cy.get("#name").should("have.value", name);
        cy.get("#email").should("have.value", email);
    });
    context("name and email", () => {
        it("can change name and email for allowed values", () => {
            cy.intercept({ method: "PATCH", url: "/profile" }).as("getProfile");

            cy.get("#name").clear();
            cy.get("#name").type("My New Name");
            cy.get("#email").clear();
            cy.get("#email").wait(500).type("new-email@email.com");
            cy.contains("button", "Save").click();
            cy.wait("@getProfile").its("response.statusCode").should("eql", 303);
            cy.contains("Your profile was updated").should("be.visible");
            cy.contains("button", name).should("not.exist");
            cy.contains("button", "My New Name").should("exist").and("be.visible");
            cy.get("#email").should("not.have.value", email).and("have.value", "new-email@email.com");
        });

        it("can't change email or name to empty values", () => {
            cy.get("#email").clear();
            cy.contains("button", "Save").click();
            cy.reload();
            cy.get("#email").should("have.value", email);

            cy.get("#name").clear();
            cy.contains("button", "Save").click();
            cy.reload();
            cy.get("#name").should("have.value", name);
        });

        it("can't change email to another user's email", () => {
            cy.intercept({ method: "PATCH", url: "/profile" }).as("getProfile");

            cy.create("App\\Models\\User", 1, {
                name: "John",
                email: "new-email@mail.com",
                password: password,
                credits: credits,
                has_password: true,
                email_verified_at: yesterday
            });

            cy.get("#email").clear();
            cy.get("#email").type("new-email@mail.com");
            cy.contains("button", "Save").click();
            cy.wait("@getProfile");
            cy.contains("The email has already been taken.").should("be.visible");
            cy.reload();
            cy.get("#email").should("have.value", email);
        });
    });
    context("password", () => {
        it("can change password", () => {
            cy.intercept({ method: "PUT", url: "/password" }).as("putPassword");
            cy.intercept({ method: "POST", url: "/logout" }).as("postLogout");

            cy.get("#password").type("new-password");
            cy.contains("button", "Update Password").click();
            cy.wait("@putPassword");
            cy.contains("button", name).click();
            cy.contains("Log Out").click();
            cy.wait("@postLogout");

            cy.visit({ route: "login" });
            cy.get("#email").type(email).wait(1500);
            cy.get("#password").focus();
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Login");
            cy.get("#password").type("new-password");

            cy.contains("button", "Log in").click().wait(4000);
            cy.location("pathname").should("eql", "/dashboard");
        });
    });
    context("delete account", () => {
        it("can cancel deleting account", () => {
            cy.contains("button", "Delete Account").click();
            cy.contains("h2", "Are you sure you want to delete your account?").should("be.visible");
            cy.contains("button", "Cancel").click();
            cy.contains("h2", "Are you sure you want to delete your account?").should("not.exist");
        });

        it("can't delete account with wrong password", () => {
            cy.intercept({ method: "DELETE", url: "/profile" }).as("deleteProfile");
            cy.contains("button", "Delete Account").click();
            cy.contains("h2", "Are you sure you want to delete your account?").should("be.visible");
            cy.get(":nth-child(5) > #password").type("wrong-password");
            cy.get(".mt-6.flex > .bg-red-600").click();
            cy.wait("@deleteProfile");
            cy.contains("The password is incorrect.").should("be.visible");
        });

        it("can delete account with valid password", () => {
            cy.intercept({ method: "DELETE", url: "/profile" }).as("deleteProfile");
            cy.contains("button", "Delete Account").click();
            cy.contains("h2", "Are you sure you want to delete your account?").should("be.visible");
            cy.get(":nth-child(5) > #password").type(password);
            cy.get(".mt-6.flex > .bg-red-600").click();
            cy.wait("@deleteProfile");
            cy.location("pathname").should("eql", "/");

            cy.visit({ route: "login" });
            cy.get("#email").type(email).wait(1500);
            cy.get("#password").focus();
            cy.get(".mx-auto.px-8.text-center").should("have.text", "Register");
            cy.get("#password").type(password);
        });
    });
});
