Application.TicketAdministration = {

  containerObject : null,
  _ticketStatusTypeClass : {
    0 : '',
    1 : 'success',
    2 : 'info',
    3 : 'error'
  },

  Init : function() {
    this.containerObject = $('#ticket-administration-container');

    this.FetchTickets({});
  },

  FetchTickets : function(information) {
    try {
      var JsonClient = new AjaxFramework.Client();
      JsonClient.setAjaxMethod('TicketAdministration.getTickets');
      JsonClient.setData(information);
      JsonClient.setRequestMethod('POST');
      JsonClient.setResponseGlue('JSON');
      JsonClient.setOkCallBack(Application.TicketAdministration.ajaxFetchTicketsOk);
      JsonClient.setErrorCallBack(Application.ajaxError);
      JsonClient.Run();
    } catch(ex){
      console.log(ex);
    }
  },

  ajaxFetchTicketsOk : function(data){
    var objectInstance = Application.TicketAdministration;

    if(data.status == 'ok') {
      var html = '';

      html += objectInstance._fetchTicketsHTMLTable(data.tickets);

      objectInstance.containerObject.html(html);
    } else {

    }
  },

  _fetchTicketsHTMLTable : function(ticketList) {
    var html = '', objectInstance = this;

    html += '<table class="table table-striped table-bordered">' +
              '<thead>' +
                '<tr>'    +
                  '<td></td>' +
                  '<td>Title</td>' +
                  '<td>Category</td>' +
                  '<td>Client Name</td>' +
                  '<td>Client Email Address</td>' +
                  '<td>Status</td>' +
                '</tr>'   +
              '</thead>' +
              '<tbody>';
    var i = 1;
    $.each(ticketList, function(key, ticket){

      html += '<tr class="' + objectInstance._fetchTicketStatusTypeClass(ticket.ticket_status_type)+ '">' +
                '<td>' + ticket.id + '</td>' +
                '<td>' + ticket.title + '</td>' +
                '<td>' + ticket.ticket_category_name + '</td>' +
                '<td>' + ticket.client_first_name + ' ' + ticket.client_last_name + '</td>' +
                '<td>' + ticket.client_email_address + '</td>' +
                '<td>' + ticket.ticket_status_name + '</td>' +
              '</tr>';

      i++;
    });

    html +=   '</tbody>' +
            '</table>';

    return html;
  },

  _fetchTicketStatusTypeClass : function(type) {
    return this._ticketStatusTypeClass[type];
  }

};