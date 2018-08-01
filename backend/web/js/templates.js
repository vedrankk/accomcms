/*
 * Modal popup
 */
function showModalMessage(message) {
    console.log(message);
    // Get the snackbar DIV
    let x = document.getElementById("snackbar");
    // Add the "show" class to DIV
    x.textContent = message;
    x.className = "show";
    

    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}