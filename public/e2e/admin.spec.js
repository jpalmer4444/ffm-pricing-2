/* 
 * Simple E2E test that navigates.
 * Salespeople -> Customers -> Products.
 */

describe('Navigates from Salespeople to Products', function () {

  it('should navigate', function () {

    browser.get('https://ffmpricing.localhost/salespeople');

    var EC = protractor.ExpectedConditions;
    // Waits for the element with class 'salespeople-btn' to be present on the dom.
    //this happens after the table has loaded and returned rows.
    browser.wait(EC.presenceOf($('tr.odd')), 15000);

    element.all(by.css('tr.odd td:nth-child(0)')).first().click();

    //now we should navigate to Customers page.
    // Waits for the element with class 'products-btn' to be present on the dom.
    //this happens after the table has loaded and returned rows.
    browser.wait(EC.presenceOf($('tr.odd')), 15000);
    
    element.all(by.css('tr.odd  td:nth-child(0)')).first().click();
    
    //now wait up to 15 seconds for the table to load.
    browser.wait(EC.presenceOf($('tr.odd')), 15000);
    
    expect(browser.getCurrentUrl())
    
    .toContain('https://ffmpricing.localhost/product/');

  });
});

describe('Admin user defaults to Salespeople page', function () {

  it('should default to salespeople', function () {

    browser.get('https://ffmpricing.localhost/');
    
    expect(browser.getCurrentUrl())
    
    .toBe('https://ffmpricing.localhost/salespeople');

  });
});




