function showHide()
{
    type = $("input[name=password]").attr("type");  

    if( type == 'text' )
    {
        $("input[name=password]").attr("type","password");
    }
    else
    {
        $("input[name=password]").attr("type","text");
    }
}