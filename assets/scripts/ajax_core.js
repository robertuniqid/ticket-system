AjaxFramework 							= 	function() {};
AjaxFramework.METHODS					=	function() {};

AjaxFramework.Errors					=	function()	{};
AjaxFramework.SERVER_ADDR 				= 'ajax';
AjaxFramework.RESPONSE_GLUE				=	"JSON";
AjaxFramework.REQUEST_METHOD			=	"POST";

AjaxFramework.Errors.ERROR_HTTP			  = 1;
AjaxFramework.Errors.ERROR_WS			    = 2;
AjaxFramework.Errors.ERROR_REQUEST		= 3;
AjaxFramework.Errors.ERROR_INTERNAL		= 4;

AjaxFramework.Client	=	function()
{
    this.data					=	null;

    this.responseOKCallBack		=	null;

    this.responseErrorCallBack	=	null;

    this.requestMethod			=	AjaxFramework.REQUEST_METHOD;

    this.ajaxMethod				=	null;

    this.serverUrl				=	AjaxFramework.SERVER_ADDR;

    this.expectedResponseGlue	=	AjaxFramework.RESPONSE_GLUE;

    this.setData = function(xData)
    {
        this.data = '&' + $.param(xData);
    };

    this.setOkCallBack	=	function(xCallBack)
    {
        this.responseOKCallBack	=	xCallBack;
    };

    this.setErrorCallBack	=	function(yCallBack)
    {
        this.responceErrorCallBack	=	yCallBack;
    };

    this.setRequestMethod	=	function(xMethod)
    {
        this.requestMethod	=	xMethod;
    };

    this.setAjaxMethod	=	function(xCallMethod)
    {
        this.ajaxMethod	=	'am=' + xCallMethod;
    };

    this.setResponseGlue	=	function(xGlueString)
    {
        this.expectedResponseGlue	=	xGlueString;
    };

    this.Run	= function()
    {
        var to_url		=	this.serverUrl;
        var strQuery	=	this.ajaxMethod + '&glue=' + this.expectedResponseGlue + this.data;
        var type		=	AjaxFramework.RESPONSE_GLUE.toLowerCase();

        try{
            var request		=	$.ajax({
                type: this.requestMethod,
                url:  to_url,
                data: strQuery,
                datatype: type
            });

            request.fail( $.proxy(this.responseErrorCallBack, this) );
            request.done($.proxy(this.responseOKCallBack, this));

        } catch(ex) {
            if(console && console.log)
            {
                console.log(ex);
            }
            else
            {
                alert(ex);
            }
        }

    };

};