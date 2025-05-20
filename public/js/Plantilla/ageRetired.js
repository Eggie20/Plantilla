// Helper function to calculate retirement date based on date of birth
function getRetirementDate(dob) {
    var birthDate = new Date(dob);
    var retirementYear = birthDate.getFullYear() + 60;  // Assuming retirement at 60 years
    var retirementDate = new Date(retirementYear, birthDate.getMonth(), birthDate.getDate());
    return retirementDate.toLocaleDateString();  // Format the date in locale-specific format
}

// Helper function to calculate age from date of birth
function getAge(birthDate) {
    var birth = new Date(birthDate);
    var today = new Date();
    var age = today.getFullYear() - birth.getFullYear();
    var m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}