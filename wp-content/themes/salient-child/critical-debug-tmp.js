const penthouse = require('penthouse');

const url = 'https://staging.orzechheating.local/lennox-ultimate-comfort-system';

const start = Date.now();
penthouse({
  url,
  css: 'assets/css/style.min.css',
  width: 375,
  height: 812,
  timeout: 60000,
  chromiumFlags: ['--ignore-certificate-errors'],
  chromePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
}).then((c) => {
  console.log(`SUCCESS in ${(Date.now()-start)/1000}s`);
}).catch(e => {
  console.log(`FAILED after ${(Date.now()-start)/1000}s : ${e.message}`);
});
