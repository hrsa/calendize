import fs from "fs";

export const activateCypressEnvFile = () => {
    if (fs.existsSync(".env.cypress")) {
        fs.renameSync(".env", ".env.backup");
        fs.renameSync(".env.cypress", ".env");
    }

    return null;
};

export const activateLocalEnvFile = () => {
    if (fs.existsSync('.env.backup')) {
        fs.renameSync('.env', '.env.cypress');
        fs.renameSync('.env.backup', '.env');
    }

    return null;
};
