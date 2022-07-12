
<div class="row">
  
  <div class="bs-example" data-example-id="simple-table"> 
    <table class="table table-bordered"> 
      <caption>Статистика поставщиков</caption> 
      <thead>
         <tr> 
           <th>#</th> 
           <th>Наименование доставщика</th> 
           <th>Все заказы</th> 
           <th>Выполненные заказы</th> 
           <th>Заработанная оплата за доставку</th> 
           <th>Оплачено</th>
           <th>Долг</th>
         </tr> 
      </thead> 
      <tbody> 
        @foreach ($table as $row)
        <tr> 
          <th scope="row">{{$loop->iteration}}</th> 
          <td>{{$row['name']}}</td> 
          <td>{{$row['total_orders_sum']}}</td> 
          <td>{{$row['total_orders_sum_4_status']}}</td> 
          <td>{{$row['total_fee']}}</td> 
          <td>{{$row['earnings']}}</td> 
          <td>{{$row['total_fee'] - $row['earnings']}}</td> 
         
          <td>
            <a href="{{route('admin.delivery-statistics.show', [$row['id']])}}">Подробнее</a>
            <a href="{{route('admin.delivery-statistics.edit', [$row['id']])}}" style="padding-left: 15px;">Оплатить долг</a>
          </td> 
        </tr>
        @endforeach
        
      </tbody> 
    </table> 
  </div>

</div>


