
<div class="row">
  







<div class="col-md-5">
  <form class="form-inline" action="{{ route('admin.statistics.show', [\Request::segment(3)]) }}">
    <div class="form-group">
      <input type="date" required class="form-control" name="date_from" id="exampleInputEmail3" placeholder="Date From">
    </div>
    <div class="form-group">
      <input type="date" required class="form-control"  name="date_to" id="exampleInputPassword3" placeholder="Date To">
    </div>
    <button type="submit" class="btn btn-default">Фильтр</button>
    <a href="{{ route('admin.statistics.show', [\Request::segment(3)]) }}" class="btn btn-default">Очистить фильтр</a>
  </form>

</div>

  
  <div class="bs-example" data-example-id="simple-table"> 
    <table class="table table-bordered"> 

     
      <caption>Поставщик - {{$vendor_name }}</caption> 

      @if($result)
      <h3>Резултат Поиска</h3> 
    @endif


      <thead>
         <tr> 
           <th>№</th> 
           <th>Итог</th> 
           {{-- <th>Системный налог</th>  --}}
           <th>Статус</th> 
           <th>Товары</th> 
           <!--<th>Налог</th> -->
           <!--<th>Оплачено</th> -->
           <!--<th>Долг</th> -->
           <th>Дата создание заказа</th> 
         </tr> 
      </thead> 
      <tbody> 
        @foreach ($orders as $row)
        <tr> 
          <th scope="row">Заказ № {{$row->id}}</th> 
          <td>{{$row->total}} UZS</td> 
          {{-- <td>{{$row->tax}}</td>  --}}
          <td>{{$row->status}}</td> 
          <td>




            <table class="table"> 
              <thead> 
                <tr> 
                  <th>#</th> 
                  <th>Наименование товара</th> 
                  <th>Количество</th>
                   <th>Общая сумма</th> 
                   <th>Подкатегория</th> 
                   <th>Процент подкатегории</th> 
                   <th>Системный Налог</th> 
                  </tr> 
                </thead>
                 <tbody>
          @foreach ($row->products as  $product)
                  <tr> 
                    <th scope="row">1</th>
                     <td>{{$product->product_name}}</td> 
                     <td>{{$product->qty}}</td>
                      <td>{{$product->total_price}}</td>
                      <td>{{App\Models\Product::find($product->product_id)->subcategory->category_name}}</td>
                      <td>{{App\Models\Product::find($product->product_id)->subcategory->tax}}</td>
                     @if($row->status == 4)
                     <td>{{ $product->tax }}</td>
                     @endif 
                     </tr> 
          @endforeach  

        </tbody>
      </table> 



          </td> 


          <!--@if($row->status == 4)-->
          <!--<td>{{$row->tax}}</td> -->
          <!--<td>{{$row->paid_tax}}</td> -->
          <!--<td>{{$row->tax - $row->paid_tax}}</td> -->

          <!--@else-->
          <!--<td>-</td> -->
          <!--<td>-</td> -->
          <!--<td>-</td> -->
          <!--@endif -->
          <td>{{$row->created_at}}</td> 

        </tr>
        @endforeach
        
      </tbody> 
    </table> 
  </div>

</div>


