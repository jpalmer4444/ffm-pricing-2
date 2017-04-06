/* 
 * Simple E2E test that logs into the webapp.
 */


describe('login page', function() {
  
  it('should login', function() {
    
    browser.ignoreSynchronization = true;
    
    browser.get('https://ffmpricing.localhost/login');

    browser.findElement(by.name('username')).sendKeys('jpalmer');
    browser.findElement(by.name('password')).sendKeys('Jj1@2016');
    browser.findElement(by.css('.btn-primary')).click();
    
    browser.ignoreSynchronization = false;

    var salespeopleLink = expect(element(by.linkText('Salespeople')));
    expect(salespeopleLink).toBeDefined();
    
  });
});




