#!/usr/bin/env node
function _asyncToGenerator(fn) { return function () { var gen = fn.apply(this, arguments); return new Promise(function (resolve, reject) { function step(key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { return Promise.resolve(value).then(function (value) { step("next", value); }, function (err) { step("throw", err); }); } } return step("next"); }); }; }

const puppeteer = require('puppeteer');

const args = process.argv.slice(2);

const action = args[0];
const url = args[1];
const options = args[2] ? JSON.parse(args[2]) : {};

let fn = _asyncToGenerator(function* () {
    let browser, page;

    try {
        let launchOptions = {};
        if (options.executablePath) {
            launchOptions[executablePath] = options.executablePath;
        }

        browser = yield puppeteer.launch(launchOptions);
        page = yield browser.newPage();

        //Set all options
        if (options.cookies) {
            for (var i = 0, len = options.cookies.length; i < len; i++) {
                yield page.setCookie(cookies[i]);
            }
        }

        if (options.extraHTTPHeaders) {
            yield page.setExtraHTTPHeaders(options.extraHTTPHeaders);
        }

        if (options.javaScriptEnabled) {
            yield page.setJavaScriptEnabled(options.javaScriptEnabled);
        }

        if (options.userAgent) {
            yield page.setUserAgent(options.userAgent);
        }

        if (options.viewport) {
            yield page.setViewport(options.viewport);
        }

        yield page.goto(url);

        if (action === 'screenshot') {
            yield page.screenshot(options);
        } else if(action === 'pdf') {
            yield page.pdf(options);
        } else {
            console.log('Invalid action: ' + action);
            process.exit(1);
        }

        yield browser.close();
    } catch (e) {
        if (browser) {
            yield browser.close();
        }

        console.error(e);
        process.exit(1);
    }
})();