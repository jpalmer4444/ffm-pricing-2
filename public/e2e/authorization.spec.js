/* 
 * Simple E2E test that logs into the webapp.
 * This test MUST be run first so the Selenium server
 * receives a valid PHPSESSID cookie that is then used 
 * in subsequent tests so we don't have to login again 
 * in other tests.
 */

describe('login page', function() {
  
  it('should login', function() {
    
    browser.ignoreSynchronization = true;
    
    browser.get('https://ffmpricing.localhost/login');

    browser.findElement(by.name('username')).sendKeys(browser.params.dev.login.username);
    browser.findElement(by.name('password')).sendKeys(browser.params.dev.login.password);
    browser.findElement(by.css('.btn-primary')).click();
    
    browser.ignoreSynchronization = false;
    
    var salespeopleLink = element(by.linkText('Salespeople'));
    expect(salespeopleLink).toBeDefined();
    
  });
});




