
<div class="row">
  
  <div class="bs-example" data-example-id="simple-table"> 
    <table class="table table-bordered"> 
      <caption>Статистика поставщиков</caption> 
      <thead>
         <tr> 
           <th>#</th> 
           <th>Наименование поставщика</th> 
           <th>Все заказы</th> 
           <th>Выполненные заказы</th> 
           <th>Системный налог</th> 
           <th>Оплачено</th> 
           <th>Долг</th> 
           <th>Действие</th> 
         </tr> 
      </thead> 
      <tbody> 
        @foreach ($table as $row)
        <tr> 
          <th scope="row">{{$loop->iteration}}</th> 
          <td>{{$row['vendor_name']}}</td> 
          <td>{{$row['total_orders_sum']}}</td> 
          <td>{{$row['total_orders_sum_4_status']}}</td> 
          <td>{{$row['total_tax']}}</td> 
          <td>{{$row['total_tax_paid']}}</td> 
          <td>{{$row['debt']}}</td> 
          <td>
            <a href="{{route('admin.statistics.show', [$row['vendor_id']])}}">Подробнее</a>
            <a href="{{route('admin.statistics.edit', [$row['vendor_id']])}}" style="padding-left: 15px;">Оплатить долг</a>
          </td> 
        </tr>
        @endforeach
        
      </tbody> 
    </table> 
  </div>

</div>


