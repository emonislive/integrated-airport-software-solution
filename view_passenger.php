<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['pid'])){
	$qry = $conn->query("SELECT *, CONCAT(Fname, ' ', Minit, ' ', Lname) as PName FROM Passenger where PID = ".$_GET['pid'])->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
?>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <dl>
                                <dt><b class="border-bottom border-primary">Passenger ID</b></dt>
                                <dd><?php echo ucwords($PID_Decode) ?></dd>
                                <dt><b class="border-bottom border-primary">Passenger Name</b></dt>
                                <dd><?php echo ucwords($PName) ?></dd>
                                <dt><b class="border-bottom border-primary">Passport Number</b></dt>
                                <dd><?php echo ucwords($PassportNo) ?></dd>
                                <dt><b class="border-bottom border-primary">Sex</b></dt>
                                <dd><?php echo ucwords($Sex) ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt><b class="border-bottom border-primary">Nationality</b></dt>
                                <dd><?php echo ucwords($Nationality) ?></dd>
                                <dt><b class="border-bottom border-primary">Date of Birth</b></dt>
                                <dd><?php echo ucwords($DOB) ?></dd>
                                <dt><b class="border-bottom border-primary">Money Spent</b></dt>
                                <?php
                                    $qry2 = $conn->query("SELECT COALESCE(CalculateTotalSpent({$_GET['pid']}), 0) as total")->fetch_assoc();
                                    echo $qry2['total'];
                                ?>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <span><b>Ticket List</b></span>
                    <div><small>This show the information of the Tickets booked.</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-condensed m-0 table-hover">
                            <thead>
                                <th>Ticket ID</th>
                                <th>Passenger ID</th>
                                <th>Route Name</th>
                                <th>Flight ID</th>
                                <th>Seat No</th>
                                <th>Class</th>
                                <th>Price</th>
                                <th>Book Time</th>
                                <th>Cancel Time</th>
                                <th>Check In</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;

                                // Select consultants who are experts on the specific model
                                $tickets = $conn->query("
                                    SELECT Ticket.TicketID, Ticket.PID, Passenger.PID_Decode, Route.RName, Flight.FlightID, Seat.SeatNum, Seat.Class, Seat.Price, Ticket.BookTime, Ticket.CheckInStatus, Ticket.CancelTime
                                    FROM Ticket
                                    JOIN Seat ON Ticket.SeatNum = Seat.SeatNum AND Ticket.FlightID = Seat.FlightID
                                    JOIN Passenger ON Ticket.PID = Passenger.PID
                                    JOIN Flight ON Ticket.FlightID = Flight.FlightID
                                    JOIN Route ON Flight.RID = Route.ID
                                    WHERE Ticket.PID = '".$_GET['pid']."'
                                ");

                                while ($row = $tickets->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class=""><?php echo $row['TicketID'] ?></td>
                                    <td class=""><?php echo $row['PID_Decode'] ?></td>
                                    <td class=""><?php echo $row['RName'] ?></td>
                                    <td class=""><?php echo $row['FlightID'] ?></td>
                                    <td class=""><?php echo $row['SeatNum'] ?></td>
                                    <td class=""><?php echo $row['Class'] ?></td>
                                    <td class=""><?php echo $row['Price'] ?></td>
                                    <td class=""><?php echo $row['BookTime'] ?></td>
                                    <td class=""><?php echo $row['CancelTime'] ?></td>
                                    <td class=""><?php echo $row['CheckInStatus'] ?></td>
                                    <td class="">
                                        <button type="button"
                                            class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="true">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a class="dropdown-item view_ticket"
                                                href="./index.php?page=view_ticket&ticketid=<?php echo $row['TicketID'] ?>"
                                                data-id="<?php echo $row['TicketID'] ?>">View</a>

                                            <?php if($row['CancelTime'] == '1970-01-01 00:00:00' && $row['CheckInStatus'] == 'No'): ?>

                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item edit_ticket"
                                                href="./index.php?page=edit_ticket&tid=<?php echo $row['TicketID'] ?>"
                                                data-id="<?php echo $row['TicketID'] ?>">Edit</a>
                                            
                                            <?php endif; ?>
                                            
                                            <!-- New dropdown menu item for Cancel action -->

                                            <?php if($row['CheckInStatus'] == 'No' && $row['CancelTime'] == '1970-01-01 00:00:00'): ?>

                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item cancel_ticket" href="javascript:void(0)"
                                                data-id="<?php echo $row['TicketID'] ?>">Cancel</a>

                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                endwhile;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
</style>

<script>
$(document).ready(function() {
    $('#list').dataTable()

    $(document).on('click', '.delete_ticket', function() {
        _conf_str("Are you sure to delete this Ticket?", "delete_ticket", [$(this).attr(
            'data-id')]);
    });

    $(document).on('click', '.cancel_ticket', function() {
        _conf_str("Are you sure to cancel this Ticket?", "cancel_ticket", [$(this).attr(
            'data-id')]);
    });
})

function delete_ticket($ticketid) {
    start_load()
    $.ajax({
        url: 'ajax.php?action=delete_ticket',
        method: 'POST',
        data: {
            ticketid: $ticketid
        },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Data successfully deleted", 'success')
                setTimeout(function() {
                    location.reload()
                }, 1500)
            }
            // else {
            //     alert_toast('Data failed to delete.', "error");
            //     setTimeout(function() {
            //         // location.replace('index.php?page=list_airplane')
            //         location.replace('index.php?page=view_passenger&pid='.$_GET['id'])
            //     }, 750)
            // }
            else {
                alert_toast('Error: ' + resp,
                    "error"); // Display the error message returned from the server
                setTimeout(function() {
                    location.reload();
                }, 750);
            }
        }.bind(this) // Bind this to the AJAX context
    })
}

function cancel_ticket($ticketid) {
    start_load()
    $.ajax({
        url: 'ajax.php?action=cancel_ticket',
        method: 'POST',
        data: {
            ticketid: $ticketid
        },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Ticket successfully canceled", 'success')
                setTimeout(function() {
                    location.reload()
                }, 1500)
            } else {
                alert_toast('Error: ' + resp, "error");
                setTimeout(function() {
                    location.reload();
                }, 750);
            }
        }.bind(this) // Bind this to the AJAX context
    })
}
</script>