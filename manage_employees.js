// Fetch companies and roles for dropdowns
fetch('get_companies_and_roles.php')
    .then(response => response.json())
    .then(data => {
        const companyDropdown = document.getElementById('company');
        const roleDropdown = document.getElementById('role');

        if (data.status === 'success') {
            // Populate companies
            data.companies.forEach(company => {
                const option = document.createElement('option');
                option.value = company.CompanyID;
                option.textContent = company.CompanyName;
                companyDropdown.appendChild(option);
            });

            // Populate roles
            data.roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.RoleID;
                option.textContent = role.RoleName;
                roleDropdown.appendChild(option);
            });
        } else {
            alert('Failed to load companies and roles.');
        }
    })
    .catch(error => {
        console.error('Error fetching companies and roles:', error);
    });

// Fetch employee data from the server
// Fetch employee data from the server
fetch('get_employees.php')
    .then(response => response.json())
    .then(data => {
        console.log('Data:', data); // Log the fetched data
        const tableBody = document.querySelector('#employee-table tbody');
        const loading = document.getElementById('loading');
        const table = document.getElementById('employee-table');

        if (data.status === 'success' && data.data) {
            loading.style.display = 'none';
            table.style.display = 'table';

            // Clear existing rows
            tableBody.innerHTML = '';

            // Populate the table with employee data
            data.data.forEach(employee => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${employee.EmployeeID}</td>
                    <td>${employee.FirstName}</td>
                    <td>${employee.LastName}</td>
                    <td>${employee.Email ? employee.Email : 'N/A'}</td>
                    <td>${employee.Phone ? employee.Phone : 'N/A'}</td>
                    <td>${employee.CompanyName}</td>
                    <td>${employee.RoleName}</td>
                    <td class="actions">
                        <button class="edit" onclick="editEmployee(${employee.EmployeeID}, '${employee.FirstName}', '${employee.LastName}', '${employee.Email ? employee.Email : ''}', '${employee.Phone ? employee.Phone : ''}', ${employee.CompanyID}, ${employee.RoleID})">Edit</button>
                        <button class="remove" onclick="removeEmployee(${employee.EmployeeID})">Remove</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            loading.textContent = data.message || 'No employee records found.';
        }
    })
    .catch(error => {
        console.error('Error fetching employee data:', error);
        document.getElementById('loading').textContent = 'Failed to load employee data. Please try again later.';
    });

document.getElementById('toggle-form-button').addEventListener('click', () => {
    const formContainer = document.getElementById('employee-form-container');
    const isEditing = document.getElementById('employee-id').value !== '';

    // Reset form if not editing
    if (!isEditing) {
        document.getElementById('employee-form').reset();
        document.getElementById('submit-button').textContent = 'Add Employee';
    }

    // Toggle visibility
    formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
    formContainer.classList.toggle('visible');
});


// Add similar logic for edit buttons to show the form when editing

// Add or edit employee
document.getElementById('employee-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const employeeId = document.getElementById('employee-id').value;
    const firstName = document.getElementById('first-name').value;
    const lastName = document.getElementById('last-name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const companyId = document.getElementById('company').value;
    const roleId = document.getElementById('role').value;

    const url = employeeId ? 'update_employee.php' : 'add_employee.php';
    const method = employeeId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ employeeId, firstName, lastName, email, phone, companyId, roleId }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(employeeId ? 'Employee updated successfully!' : 'Employee added successfully!');
                window.location.reload(); // Refresh the page to reflect changes
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to process request. Please try again later.');
        });
});

// Edit employee
function editEmployee(employeeId, firstName, lastName, email, phone, companyId, roleId) {
    const formContainer = document.getElementById('employee-form-container');

    // Populate form fields
    document.getElementById('employee-id').value = employeeId;
    document.getElementById('first-name').value = firstName;
    document.getElementById('last-name').value = lastName;
    document.getElementById('email').value = email;
    document.getElementById('phone').value = phone;
    document.getElementById('company').value = companyId;
    document.getElementById('role').value = roleId;

    // Show form and change button text
    formContainer.style.display = 'block';
    document.getElementById('submit-button').textContent = 'Update Employee';

    // Add animation class
    formContainer.classList.add('visible');

    // Smooth scroll to form
    formContainer.scrollIntoView({ behavior: 'smooth' });

}

// Remove employee
function removeEmployee(employeeId) {
    if (confirm('Are you sure you want to remove this employee?')) {
        fetch('remove_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ employee_id: employeeId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Employee removed successfully!');
                    window.location.reload(); // Refresh the page to reflect changes
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove employee. Please try again later.');
            });
    }
}
