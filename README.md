bsdImporterPlugin
=================
*symfony 1.4* plugin for importing CSV and Excel files  
  
author Micah Breedlove <Micah@BlueShamrock.com>  
package bsdImporterPlugin (com.blueshamrock.importer)  

Symfony 1.4 Plugin which simplifies CSV and Excel imports.  
Create your own classes which extend the appropriate class and with in a few minutes you 
will be ready to being the import process.  
  
Your extended classes will require the following functions:  
 * execute($row, $dryRun = false, $options = array()) - The execute function accepts the (array) row to be processed and (boolean) if the execute should be treated as dry run and lastly (array) options.  
 * executeAll($dryRun = false, $options = array()) - The executeAll function accepts (boolean) if the execute should be treated as dry run and lastly (array) options.  
 * validationFailed($failureCode) - The validationFailed function accepts the (CONST string) validation Code of the given error. It allows you to define what happens when an error occurs.
  
Execution
---------
Execution in your application occurs by using the processImport function. processImport accepts 3 arguments:  
 * **dryRun** (*default: false*)  
 * **allAsOne** (process all rows instead of row by row. *default: false*)  
 * **options** (*default: empty array*)   
All three (3) of these arguments are passed to the appropriate execute(All) function.  
The following examples assumes you have extended the appropriate importer class (in this example bsdPaymentCSVImport extends PluginBsdImporterCsv):  
    
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->processImport(true, false); // process this file row by row in a dryRun  
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->setValidation("row"); // execute validation row-by-row as being processed  
    $payments->processImport(false, false); // process this file row-by-row  (default: could also be executed as $payments->processImport())  
      
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->processImport(false, true); // process this file as one chunk  
      
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->setValidation("no"); // execute NO validation  (you obviously live on the edge)  
    $payments->processImport(false, false); // process this file row-by-row   
      
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->setValidation("pre"); // execute validation before processing any data (default)  
    $payments->processImport(false, true); // process this file as one chunk  


