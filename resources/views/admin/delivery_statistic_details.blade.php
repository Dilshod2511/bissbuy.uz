
<div class="row">
  







<div class="col-md-5">
  <form class="form-inline" action="{{ route('admin.delivery-statistics.show', [\Request::segment(3)]) }}">
    <div class="form-group">
      <input type="date" required class="form-control" name="date_from" id="exampleInputEmail3" placeholder="Date From">
    </div>
    <div class="form-group">
      <input type="date" required class="form-control"  name="date_to" id="exampleInputPassword3" placeholder="Date To">
    </div>
    <button type="submit" class="btn btn-default">Фильтр</button>
    <a href="{{ route('admin.delivery-statistics.show', [\Request::segment(3)]) }}" class="btn btn-default">Очистить фильтр</a>
  </form>

</div>

  
  <div class="bs-example" data-example-id="simple-table"> 
    <table class="table table-bordered"> 

     
      <caption>Поставщик - {{$name }}</caption> 

      @if($result)
      <h3>Резултат Поиска</h3> 
    @endif
      <thead>
         <tr> 
           <th>№</th> 
           <th>Итог</th> 
           <th>Статус</th> 
           <th>Товары</th>  
           <th>Дистанция</th> 
           <th>Сумма доставки</th> 
           <th>Дата создание заказа</th> 
         </tr> 
      </thead> 
      <tbody> 
        @foreach ($orders as $row)
        <tr> 
          <th scope="row">Заказ № {{$row->id}}</th> 
          <td>{{$row->total}} UZS</td> 
          <td>{{$row->status}}</td> 
          <td>
              <table class="table"> 
                        <thead> 
                          <tr> 
                            <th>#</th> 
                            <th>Наименование товара</th> 
                            <th>Количество</th>
                            <th>Общая сумма</th> 
                            </tr> 
                          </thead>
                          <tbody>
                    @foreach ($row->products as  $product)
                            <tr> 
                              <th scope="row">1</th>
                              <td>{{$product->product_name}}</td> 
                              <td>{{$product->qty}}</td>
                                <td>{{$product->total_price}}</td>
                              </tr> 
                    @endforeach  
                  </tbody>
                </table> 
          </td> 
          <td>{{$row->distance}}</td> 
          <td>{{$row->delivery_amount}}</td> 
          <td>{{$row->created_at}}</td> 
        </tr>
        @endforeach 
      </tbody> 
    </table> 
  </div>
</div>


