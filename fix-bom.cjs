const fs = require("fs");
const path = require("path");

function walk(dir) {
    fs.readdirSync(dir).forEach((file) => {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            if (!["node_modules", ".git"].includes(file)) {
                walk(fullPath);
            }
        } else if (fullPath.endsWith(".php")) {
            let content = fs.readFileSync(fullPath);
            if (
                content[0] === 0xef &&
                content[1] === 0xbb &&
                content[2] === 0xbf
            ) {
                fs.writeFileSync(fullPath, content.subarray(3));
            } else if (content[0] === 0xff && content[1] === 0xfe) {
                // UTF-16LE, convert to utf-8 without BOM
                const str = content.toString("utf16le");
                fs.writeFileSync(fullPath, Buffer.from(str, "utf8"));
            }
        }
    });
}
walk(process.cwd());
console.log("Fixed BOMs");
