
# Enhanced Manual Test Cases with Actual vs Expected Output Check

$ManualTests = @(
    @{ TestCaseID = "TC-M-001"; Title = "Book a new flight ticket"; Precondition = "Flight with available seats exists";
       Steps = "1. Open 'book_flight.php'\n2. Select flight and seat\n3. Submit form"; Expected = "Ticket is created and listed in 'view_ticket'";
       Actual = "Ticket is created and listed in 'view_ticket'" },
       
    @{ TestCaseID = "TC-M-002"; Title = "Add new passenger"; Precondition = "Logged in as admin";
       Steps = "1. Open 'new_passenger.php'\n2. Fill in form\n3. Submit"; Expected = "Passenger is added and appears in the list";
       Actual = "Passenger is added and appears in the list" },
       
    @{ TestCaseID = "TC-M-003"; Title = "View top passengers graph"; Precondition = "Tickets exist with prices";
       Steps = "1. Open 'graph_top_passenger.php'"; Expected = "Graph displays top spenders";
       Actual = "Graph fails to load" },
       
    @{ TestCaseID = "TC-M-004"; Title = "Edit a flight"; Precondition = "Flight already added";
       Steps = "1. Go to 'list_flight.php'\n2. Click Edit\n3. Change info\n4. Submit"; Expected = "Flight info updates correctly";
       Actual = "Flight info updates correctly" },
       
    @{ TestCaseID = "TC-M-005"; Title = "Cancel a ticket"; Precondition = "Ticket exists";
       Steps = "1. Open 'edit_ticket.php'\n2. Set CancelTime\n3. Submit"; Expected = "Seat becomes available again";
       Actual = "Seat becomes available again" }
)

foreach ($test in $ManualTests) {
    $status = if ($test.Expected -eq $test.Actual) { "Passed" } else { "Failed" }

    Write-Host "TestCaseID : $($test.TestCaseID)"
    Write-Host "Title      : $($test.Title)"
    Write-Host "Expected   : $($test.Expected)"
    Write-Host "Actual     : $($test.Actual)"
    Write-Host "Status     : $status"
    Write-Host "`n------------------------------------------`n"
}
